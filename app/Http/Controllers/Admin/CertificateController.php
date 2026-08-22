<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\CertificateStore;
use App\Support\SiteSettings;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $students = collect(AdmissionStore::all())->filter(fn (array $student): bool => ($student['status'] ?? '') === 'admitted')->keyBy('id');
        $search = strtolower(trim((string) $request->query('search')));
        $type = trim((string) $request->query('type'));
        $items = array_values(array_filter(array_map(function (array $item) use ($students): array {
            $item['student'] = $students->get($item['student_id']);
            return $item;
        }, CertificateStore::all()), function (array $item) use ($search, $type): bool {
            $student = $item['student'] ?? [];
            $haystack = strtolower(($item['certificate_no'] ?? '').' '.($item['verification_code'] ?? '').' '.($student['student_name'] ?? '').' '.($student['roll_no'] ?? '').' '.($student['application_no'] ?? ''));
            return $student && (! $search || str_contains($haystack, $search)) && (! $type || ($item['type'] ?? '') === $type);
        }));

        return view('admin.certificates.index', [
            'certificates' => $items,
            'students' => $students->values(),
            'typeCounts' => collect($items)->countBy('type'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required','string'],
            'type' => ['required','in:completion,merit,participation'],
            'title' => ['required','string','max:150'],
            'issue_date' => ['required','date','before_or_equal:today'],
            'completion_date' => ['nullable','date','before_or_equal:today'],
            'grade' => ['nullable','string','max:20'],
            'description' => ['nullable','string','max:500'],
        ]);
        $student = AdmissionStore::find($data['student_id']);
        abort_unless($student && ($student['status'] ?? '') === 'admitted', 404);
        CertificateStore::add($data);
        return back()->with('success', 'Certificate issued successfully.');
    }

    public function print(string $id)
    {
        $certificate = CertificateStore::find($id);
        abort_unless($certificate, 404);
        $student = AdmissionStore::find($certificate['student_id']);
        abort_unless($student, 404);
        return view('admin.certificates.print', [
            'certificate' => $certificate,
            'student' => $student,
            'course' => Course::where('code', $student['course_code'] ?? '')->first(),
            'settings' => SiteSettings::all(),
        ]);
    }

    public function destroy(string $id)
    {
        abort_unless(CertificateStore::remove($id), 404);
        return back()->with('success', 'Certificate revoked and deleted.');
    }
}
