@extends('admin.layout')
@section('title','Student Attendance')
@section('content')
<div class="cards attendance-summary">
    <div class="card blue"><small>Students Listed</small><strong>{{ count($students) }}</strong></div>
    <div class="card green"><small>Present</small><strong>{{ $presentCount }}</strong></div>
    <div class="card orange"><small>Absent</small><strong>{{ $absentCount }}</strong></div>
    <div class="card purple"><small>Leave</small><strong>{{ $leaveCount }}</strong></div>
    <div class="card dark"><small>Unmarked</small><strong>{{ $unmarkedCount }}</strong></div>
</div>
<section class="panel">
<form class="attendance-filter" method="get">
<label>Attendance Date<input type="date" name="date" value="{{ $date }}"></label>
<label>Batch Name / Timing<input name="batch" value="{{ $batch }}" placeholder="e.g. DCA Morning"></label>
<button class="btn">Load Students</button><a href="{{ route('admin.attendance.index') }}">Today</a>
<a class="export-link" href="{{ route('admin.attendance.export',['date'=>$date,'batch'=>$batch]) }}">Export CSV ↓</a><a class="export-link" href="{{ route('admin.attendance.report',['month'=>substr($date,0,7),'batch'=>$batch]) }}">Monthly Report →</a>
</form>
</section>
<section class="panel"><div class="panel-title"><div><small>DAILY ATTENDANCE REGISTER</small><h2>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2></div><div class="attendance-bulk"><button type="button" data-mark-all="present">Mark All Present</button><button type="button" data-mark-all="absent">Mark All Absent</button></div></div>
@if(count($students))
<form method="post" action="{{ route('admin.attendance.store') }}" id="attendanceForm">@csrf
<input type="hidden" name="date" value="{{ $date }}"><input type="hidden" name="batch" value="{{ $batch }}">
<div class="attendance-list">
@foreach($students as $student)
@php $record=$records[$student['id']] ?? []; @endphp
<article>
<div class="attendance-student"><span class="course-code">{{ $student['course_code'] }}</span><div><b>{{ $student['student_name'] }}</b><small>{{ $student['roll_no'] ?? $student['application_no'] }} · {{ $student['batch_name'] ?? 'Batch not assigned' }} · {{ $student['batch_time'] ?? $student['preferred_time'] ?? 'Timing not set' }}</small></div></div>
<div class="attendance-options">
<label class="present"><input type="radio" name="attendance[{{ $student['id'] }}]" value="present" @checked(($record['status'] ?? '')==='present') required><span>✓ Present</span></label>
<label class="absent"><input type="radio" name="attendance[{{ $student['id'] }}]" value="absent" @checked(($record['status'] ?? '')==='absent') required><span>× Absent</span></label>
<label class="leave"><input type="radio" name="attendance[{{ $student['id'] }}]" value="leave" @checked(($record['status'] ?? '')==='leave') required><span>○ Leave</span></label>
</div>
<input class="attendance-note" name="notes[{{ $student['id'] }}]" value="{{ $record['note'] ?? '' }}" maxlength="160" placeholder="Optional note">
</article>
@endforeach
</div>
<div class="attendance-save"><div><b>Save attendance for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</b><small>All listed students must have a status.</small></div><button class="btn">Save Attendance</button></div>
</form>
@else<div class="empty"><b>No active admitted students found</b><p>Students को admitted और active करने तथा batch assign करने के बाद यहाँ दिखाई देंगे।</p><a href="{{ route('admin.students.index') }}">Open Students Register →</a></div>@endif
</section>
<script>
document.querySelectorAll('[data-mark-all]').forEach(button=>button.addEventListener('click',()=>{
 const value=button.dataset.markAll;
 document.querySelectorAll('input[type="radio"][value="'+value+'"]').forEach(input=>input.checked=true);
}));
</script>
@endsection
