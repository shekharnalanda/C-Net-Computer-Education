<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $allItems = $this->withFinancialDefaults(AdmissionStore::all());
        $items = $allItems;
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $course = trim((string) $request->query('course'));

        $items = array_values(array_filter($items, function (array $item) use ($search, $status, $course) {
            $haystack = strtolower(($item['application_no'] ?? '').' '.($item['student_name'] ?? '').' '.($item['phone'] ?? '').' '.($item['guardian_name'] ?? ''));
            return (! $search || str_contains($haystack, strtolower($search)))
                && (! $status || ($item['status'] ?? '') === $status)
                && (! $course || ($item['course_code'] ?? '') === $course);
        }));

        return view('admin.admissions.index', [
            'applications' => $items,
            'allApplications' => $allItems,
            'totalFees' => collect($allItems)->sum('course_fee'),
            'totalPaid' => collect($allItems)->sum('paid_amount'),
            'totalBalance' => collect($allItems)->sum('balance_amount'),
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $data = $request->validate(['status' => ['required','in:pending,contacted,verified,admitted,rejected']]);
        abort_unless(AdmissionStore::updateStatus($id, $data['status']), 404);

        return back()->with('success', 'Application status updated.');
    }

    public function updatePayment(Request $request, string $id)
    {
        $data = $request->validate([
            'course_fee' => ['required','numeric','min:0','max:99999999.99'],
            'paid_amount' => ['required','numeric','min:0','lte:course_fee'],
            'payment_note' => ['nullable','string','max:255'],
        ]);
        abort_unless(AdmissionStore::updatePayment($id, (float) $data['course_fee'], (float) $data['paid_amount'], $data['payment_note'] ?? null), 404);

        return back()->with('success', 'Fee record updated successfully.');
    }

    public function receipt(string $id)
    {
        $item = AdmissionStore::find($id);
        abort_unless($item, 404);
        $application = $this->withFinancialDefaults([$item])[0];
        $course = Course::where('code', $application['course_code'] ?? '')->first();

        return view('admin.admissions.receipt', [
            'application' => $application,
            'course' => $course,
            'settings' => SiteSettings::all(),
        ]);
    }

    public function destroy(string $id)
    {
        abort_unless(AdmissionStore::remove($id), 404);

        return back()->with('success', 'Application deleted.');
    }

    public function export(): StreamedResponse
    {
        $items = $this->withFinancialDefaults(AdmissionStore::all());

        return response()->streamDownload(function () use ($items) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Application No','Date','Student','DOB','Gender','Guardian','Phone','Email','Address','City','Qualification','Course','Course Fee','Paid','Balance','Payment Status','Receipt No','Preferred Time','Admission Status']);
            foreach ($items as $item) {
                fputcsv($output, [
                    $item['application_no'] ?? '', $item['created_at'] ?? '', $item['student_name'] ?? '',
                    $item['dob'] ?? '', $item['gender'] ?? '', $item['guardian_name'] ?? '', $item['phone'] ?? '',
                    $item['email'] ?? '', $item['address'] ?? '', $item['city'] ?? '', $item['qualification'] ?? '',
                    $item['course_code'] ?? '', $item['course_fee'], $item['paid_amount'], $item['balance_amount'],
                    $item['payment_status'], $item['receipt_no'] ?? '', $item['preferred_time'] ?? '', $item['status'] ?? '',
                ]);
            }
            fclose($output);
        }, 'cnet-admissions-fees-'.date('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function withFinancialDefaults(array $items): array
    {
        $fees = Course::pluck('fee_amount', 'code');
        return array_map(function (array $item) use ($fees): array {
            $fee = (float) ($item['course_fee'] ?? $fees[$item['course_code'] ?? ''] ?? 0);
            $paid = (float) ($item['paid_amount'] ?? 0);
            $item['course_fee'] = $fee;
            $item['paid_amount'] = $paid;
            $item['balance_amount'] = (float) ($item['balance_amount'] ?? max(0, $fee - $paid));
            $item['payment_status'] = $item['payment_status'] ?? ($paid <= 0 ? 'unpaid' : ($item['balance_amount'] > 0 ? 'partial' : 'paid'));
            return $item;
        }, $items);
    }
}
