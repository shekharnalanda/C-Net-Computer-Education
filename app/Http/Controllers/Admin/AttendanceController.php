<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdmissionStore;
use App\Support\AttendanceStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $this->date($request);
        $batch = strtolower(trim((string) $request->query('batch')));
        $students = $this->students($batch);
        $records = AttendanceStore::forDate($date);
        $counts = collect($records)->countBy('status');

        return view('admin.attendance.index', [
            'date' => $date,
            'batch' => $request->query('batch', ''),
            'students' => $students,
            'records' => $records,
            'presentCount' => $counts['present'] ?? 0,
            'absentCount' => $counts['absent'] ?? 0,
            'leaveCount' => $counts['leave'] ?? 0,
            'unmarkedCount' => max(0, count($students) - count($records)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required','date'],
            'attendance' => ['required','array'],
            'attendance.*' => ['required', Rule::in(['present','absent','leave'])],
            'notes' => ['nullable','array'],
            'notes.*' => ['nullable','string','max:160'],
            'batch' => ['nullable','string','max:100'],
        ]);
        AttendanceStore::saveBulk($data['date'], $data['attendance'], $data['notes'] ?? []);

        return redirect()->route('admin.attendance.index', array_filter(['date' => $data['date'], 'batch' => $data['batch'] ?? null]))
            ->with('success', 'Attendance saved for '.Carbon::parse($data['date'])->format('d M Y').'.');
    }

    public function export(Request $request): StreamedResponse
    {
        $date = $this->date($request);
        $students = $this->students(strtolower(trim((string) $request->query('batch'))));
        $records = AttendanceStore::forDate($date);

        return response()->streamDownload(function () use ($date, $students, $records) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date','Roll No','Student','Application No','Course','Batch','Batch Time','Status','Note']);
            foreach ($students as $student) {
                $record = $records[$student['id']] ?? [];
                fputcsv($output, [
                    $date, $student['roll_no'] ?? '', $student['student_name'] ?? '', $student['application_no'] ?? '',
                    $student['course_code'] ?? '', $student['batch_name'] ?? '', $student['batch_time'] ?? '',
                    $record['status'] ?? 'unmarked', $record['note'] ?? '',
                ]);
            }
            fclose($output);
        }, 'cnet-attendance-'.$date.'.csv', ['Content-Type' => 'text/csv']);
    }

    private function students(string $batch = ''): array
    {
        return array_values(array_filter(AdmissionStore::all(), function (array $student) use ($batch): bool {
            $isActive = ($student['status'] ?? '') === 'admitted' && ($student['student_status'] ?? 'active') === 'active';
            $batchText = strtolower(($student['batch_name'] ?? '').' '.($student['batch_time'] ?? ''));
            return $isActive && (! $batch || str_contains($batchText, $batch));
        }));
    }

    private function date(Request $request): string
    {
        try {
            return Carbon::parse((string) $request->query('date', today()->toDateString()))->toDateString();
        } catch (\Throwable) {
            return today()->toDateString();
        }
    }
}
