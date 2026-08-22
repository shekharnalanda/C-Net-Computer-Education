@extends('admin.layout')
@section('title','Monthly Attendance Report')
@section('content')
<div class="cards attendance-report-summary">
    <div class="card blue"><small>Students</small><strong>{{ $studentCount }}</strong></div>
    <div class="card green"><small>Total Present</small><strong>{{ $presentTotal }}</strong></div>
    <div class="card orange"><small>Total Absent</small><strong>{{ $absentTotal }}</strong></div>
    <div class="card purple"><small>Total Leave</small><strong>{{ $leaveTotal }}</strong></div>
    <div class="card cyan"><small>Average Attendance</small><strong>{{ $averagePercentage }}%</strong></div>
</div>
<section class="panel"><form class="monthly-report-filter" method="get">
<label>Report Month<input type="month" name="month" value="{{ $month }}"></label>
<label>Course<select name="course"><option value="">All courses</option>@foreach($courses as $item)<option value="{{ $item->code }}" @selected($course===$item->code)>{{ $item->code }} — {{ $item->title }}</option>@endforeach</select></label>
<label>Batch<input name="batch" value="{{ $batch }}" placeholder="Batch name or timing"></label>
<button class="btn">Generate Report</button><a href="{{ route('admin.attendance.report') }}">Current Month</a>
<a class="export-link" href="{{ route('admin.attendance.report.export',['month'=>$month,'course'=>$course,'batch'=>$batch]) }}">Download CSV ↓</a>
</form></section>
<section class="panel"><div class="panel-title"><div><small>MONTHLY STUDENT ATTENDANCE</small><h2>{{ $monthLabel }}</h2></div><a href="{{ route('admin.attendance.index') }}">Daily Attendance →</a></div>
@if(count($rows))
<div class="monthly-report-table"><table><thead><tr><th>Student</th><th>Roll / Application</th><th>Course & Batch</th><th>Present</th><th>Absent</th><th>Leave</th><th>Marked Days</th><th>Attendance %</th></tr></thead><tbody>
@foreach($rows as $row)<tr>
<td><b>{{ $row['student']['student_name'] }}</b><small>{{ $row['student']['phone'] }}</small></td>
<td><b>{{ $row['student']['roll_no'] ?? 'Not assigned' }}</b><small>{{ $row['student']['application_no'] }}</small></td>
<td><span class="course-tag">{{ $row['student']['course_code'] }}</span><small>{{ $row['student']['batch_name'] ?? 'Batch not assigned' }}</small></td>
<td><span class="count-present">{{ $row['present'] }}</span></td><td><span class="count-absent">{{ $row['absent'] }}</span></td><td><span class="count-leave">{{ $row['leave'] }}</span></td><td>{{ $row['marked'] }}</td>
<td><div class="attendance-percent"><b>{{ $row['percentage'] }}%</b><span><i style="width:{{ min(100,$row['percentage']) }}%"></i></span></div></td>
</tr>@endforeach
</tbody></table></div>
@else<div class="empty"><b>No student records for this report</b><p>Course या batch filter बदलें, अथवा admitted students की attendance दर्ज करें।</p><a href="{{ route('admin.attendance.index') }}">Open Daily Attendance →</a></div>@endif
</section>
@endsection
