<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdmissionStore;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $items = AdmissionStore::all();
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
            'allApplications' => AdmissionStore::all(),
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $data = $request->validate(['status' => ['required','in:pending,contacted,verified,admitted,rejected']]);
        abort_unless(AdmissionStore::updateStatus($id, $data['status']), 404);

        return back()->with('success', 'Application status updated.');
    }

    public function destroy(string $id)
    {
        abort_unless(AdmissionStore::remove($id), 404);

        return back()->with('success', 'Application deleted.');
    }

    public function export(): StreamedResponse
    {
        $items = AdmissionStore::all();

        return response()->streamDownload(function () use ($items) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Application No','Date','Student','DOB','Gender','Guardian','Phone','Email','Address','City','Qualification','Course','Preferred Time','Status']);
            foreach ($items as $item) {
                fputcsv($output, [
                    $item['application_no'] ?? '', $item['created_at'] ?? '', $item['student_name'] ?? '',
                    $item['dob'] ?? '', $item['gender'] ?? '', $item['guardian_name'] ?? '', $item['phone'] ?? '',
                    $item['email'] ?? '', $item['address'] ?? '', $item['city'] ?? '', $item['qualification'] ?? '',
                    $item['course_code'] ?? '', $item['preferred_time'] ?? '', $item['status'] ?? '',
                ]);
            }
            fclose($output);
        }, 'cnet-admissions-'.date('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }
}
