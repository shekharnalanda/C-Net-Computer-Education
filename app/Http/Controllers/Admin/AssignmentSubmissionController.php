<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdmissionStore;
use App\Support\AssignmentSubmissionStore;
use App\Support\LearningResourceStore;
use Illuminate\Http\Request;

class AssignmentSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $search = strtolower(trim((string) $request->query('search')));
        $status = trim((string) $request->query('status'));
        $course = trim((string) $request->query('course'));
        $students = collect(AdmissionStore::all())->keyBy('id');
        $resources = collect(LearningResourceStore::all())->keyBy('id');

        $submissions = array_values(array_filter(array_map(function (array $row) use ($students, $resources): array {
            $row['student'] = $students->get($row['student_id']);
            $row['resource'] = $resources->get($row['resource_id']);
            return $row;
        }, AssignmentSubmissionStore::all()), function (array $row) use ($search, $status, $course): bool {
            $haystack = strtolower(($row['student']['student_name'] ?? '').' '.($row['student']['application_no'] ?? '').' '.($row['resource']['title'] ?? ''));
            return (! $search || str_contains($haystack, $search))
                && (! $status || ($row['status'] ?? '') === $status)
                && (! $course || ($row['course_code'] ?? '') === $course);
        }));

        return view('admin.assignments.index', [
            'submissions' => $submissions,
            'courses' => collect(AdmissionStore::all())->pluck('course_code')->filter()->unique()->sort()->values(),
            'pending' => collect($submissions)->where('status', 'submitted')->count(),
            'reviewed' => collect($submissions)->where('status', 'reviewed')->count(),
        ]);
    }

    public function review(Request $request, string $id)
    {
        abort_unless(AssignmentSubmissionStore::find($id), 404);
        $data = $request->validate([
            'marks' => ['required','numeric','min:0','max:100'],
            'feedback' => ['nullable','string','max:1000'],
        ]);
        AssignmentSubmissionStore::review($id, $data);
        return back()->with('success', 'Assignment reviewed successfully.');
    }

    public function destroy(string $id)
    {
        abort_unless(AssignmentSubmissionStore::remove($id), 404);
        return back()->with('success', 'Assignment submission deleted.');
    }
}
