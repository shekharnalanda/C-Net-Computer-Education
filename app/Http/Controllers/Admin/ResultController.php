<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\AdmissionStore;
use App\Support\ExamResultStore;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $students = collect(AdmissionStore::all())->filter(fn (array $student): bool => ($student['status'] ?? '') === 'admitted')->keyBy('id');
        $search = strtolower(trim((string) $request->query('search')));
        $course = trim((string) $request->query('course'));
        $status = trim((string) $request->query('result_status'));
        $results = array_values(array_filter(array_map(function (array $result) use ($students): array {
            $result['student'] = $students->get($result['student_id']);
            return $result;
        }, ExamResultStore::all()), function (array $result) use ($search, $course, $status): bool {
            $student = $result['student'] ?? [];
            $haystack = strtolower(($result['result_no'] ?? '').' '.($result['exam_name'] ?? '').' '.($student['student_name'] ?? '').' '.($student['application_no'] ?? '').' '.($student['roll_no'] ?? ''));
            return $student
                && (! $search || str_contains($haystack, $search))
                && (! $course || ($student['course_code'] ?? '') === $course)
                && (! $status || ($result['result_status'] ?? '') === $status);
        }));

        return view('admin.results.index', [
            'results' => $results,
            'students' => $students->values(),
            'courses' => Course::orderBy('title')->get(['code','title']),
            'passed' => collect($results)->where('result_status', 'pass')->count(),
            'failed' => collect($results)->where('result_status', 'fail')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required','string'],
            'exam_name' => ['required','string','max:150'],
            'exam_date' => ['required','date','before_or_equal:today'],
            'subject_names' => ['required','array','min:1','max:12'],
            'subject_names.*' => ['nullable','string','max:100'],
            'max_marks' => ['required','array','min:1','max:12'],
            'max_marks.*' => ['nullable','numeric','min:1','max:1000'],
            'obtained_marks' => ['required','array','min:1','max:12'],
            'obtained_marks.*' => ['nullable','numeric','min:0','max:1000'],
            'remarks' => ['nullable','string','max:255'],
        ]);
        $student = AdmissionStore::find($data['student_id']);
        abort_unless($student && ($student['status'] ?? '') === 'admitted', 404);

        $subjects = [];
        foreach ($data['subject_names'] as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') continue;
            $max = (float) ($data['max_marks'][$index] ?? 0);
            $obtained = (float) ($data['obtained_marks'][$index] ?? 0);
            if ($max <= 0 || $obtained > $max) {
                throw ValidationException::withMessages(['obtained_marks' => 'Obtained marks cannot exceed maximum marks.']);
            }
            $subjects[] = ['name' => $name, 'max_marks' => $max, 'obtained_marks' => $obtained, 'status' => (($obtained / $max) * 100) >= 33 ? 'pass' : 'fail'];
        }
        if (! count($subjects)) {
            throw ValidationException::withMessages(['subject_names' => 'Enter at least one subject.']);
        }
        $maxTotal = (float) collect($subjects)->sum('max_marks');
        $obtainedTotal = (float) collect($subjects)->sum('obtained_marks');
        $percentage = $maxTotal > 0 ? round(($obtainedTotal / $maxTotal) * 100, 2) : 0;
        $resultStatus = collect($subjects)->contains('status', 'fail') ? 'fail' : 'pass';

        ExamResultStore::add([
            'student_id' => $data['student_id'],
            'exam_name' => $data['exam_name'],
            'exam_date' => $data['exam_date'],
            'subjects' => $subjects,
            'max_total' => $maxTotal,
            'obtained_total' => $obtainedTotal,
            'percentage' => $percentage,
            'grade' => $resultStatus === 'fail' ? 'F' : $this->grade($percentage),
            'result_status' => $resultStatus,
            'remarks' => $data['remarks'] ?? null,
        ]);

        return back()->with('success', 'Exam result published successfully.');
    }

    public function marksheet(string $id)
    {
        $result = ExamResultStore::find($id);
        abort_unless($result, 404);
        $student = AdmissionStore::find($result['student_id']);
        abort_unless($student, 404);

        return view('admin.results.marksheet', [
            'result' => $result,
            'student' => $student,
            'course' => Course::where('code', $student['course_code'] ?? '')->first(),
            'settings' => SiteSettings::all(),
        ]);
    }

    public function destroy(string $id)
    {
        abort_unless(ExamResultStore::remove($id), 404);
        return back()->with('success', 'Exam result deleted.');
    }

    private function grade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C',
            default => 'D',
        };
    }
}
