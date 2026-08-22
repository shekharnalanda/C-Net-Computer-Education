@extends('admin.layout')
@section('title','Exams & Results')
@section('content')
<div class="cards result-summary">
    <div class="card blue"><small>Published Results</small><strong>{{ count($results) }}</strong></div>
    <div class="card green"><small>Passed</small><strong>{{ $passed }}</strong></div>
    <div class="card orange"><small>Needs Improvement</small><strong>{{ $failed }}</strong></div>
    <div class="card purple"><small>Admitted Students</small><strong>{{ count($students) }}</strong></div>
</div>

<div class="result-admin-grid">
<section class="panel result-entry"><div class="panel-title"><div><small>NEW EXAM RESULT</small><h2>Publish marks</h2></div></div>
<form method="post" action="{{ route('admin.results.store') }}">@csrf
<div class="form-grid">
<label>Student<select name="student_id" required><option value="">Select admitted student</option>@foreach($students as $student)<option value="{{ $student['id'] }}" @selected(old('student_id')===$student['id'])>{{ $student['student_name'] }} · {{ $student['course_code'] }} · {{ $student['roll_no'] ?? $student['application_no'] }}</option>@endforeach</select></label>
<label>Exam Name<input name="exam_name" value="{{ old('exam_name') }}" placeholder="Final Examination 2026" maxlength="150" required></label>
<label>Exam Date<input type="date" name="exam_date" value="{{ old('exam_date',now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></label>
</div>
<div class="subject-entry"><div class="subject-head"><b>Subject</b><b>Maximum Marks</b><b>Obtained Marks</b></div>
@for($i=0;$i<6;$i++)<div class="subject-row"><input name="subject_names[]" value="{{ old('subject_names.'.$i) }}" placeholder="Subject {{ $i+1 }}"><input type="number" name="max_marks[]" value="{{ old('max_marks.'.$i,$i===0 ? 100 : '') }}" min="1" max="1000" step="0.01" placeholder="100"><input type="number" name="obtained_marks[]" value="{{ old('obtained_marks.'.$i) }}" min="0" max="1000" step="0.01" placeholder="Marks"></div>@endfor
</div>
<label class="result-remarks">Remarks<textarea name="remarks" rows="2" maxlength="255" placeholder="Optional teacher remarks">{{ old('remarks') }}</textarea></label>
<button class="btn">Calculate & Publish Result</button>
</form></section>

<section class="panel result-list-panel"><form class="result-filter" method="get"><input name="search" value="{{ request('search') }}" placeholder="Student, roll, exam or result no..."><select name="course"><option value="">All Courses</option>@foreach($courses as $course)<option value="{{ $course->code }}" @selected(request('course')===$course->code)>{{ $course->code }}</option>@endforeach</select><select name="result_status"><option value="">All Results</option><option value="pass" @selected(request('result_status')==='pass')>Pass</option><option value="fail" @selected(request('result_status')==='fail')>Needs Improvement</option></select><button class="soft-btn">Search</button><a href="{{ route('admin.results.index') }}">Clear</a></form>
@if(count($results))<div class="result-records">@foreach($results as $result)<article>
<div class="result-score status-{{ $result['result_status'] }}"><strong>{{ number_format($result['percentage'],1) }}%</strong><small>Grade {{ $result['grade'] }}</small></div>
<div><small>{{ $result['result_no'] }}</small><h3>{{ $result['student']['student_name'] }}</h3><p>{{ $result['exam_name'] }} · {{ \Carbon\Carbon::parse($result['exam_date'])->format('d M Y') }}</p><span>{{ $result['student']['course_code'] }} · {{ $result['student']['roll_no'] ?? $result['student']['application_no'] }}</span></div>
<div class="result-actions"><a href="{{ route('admin.results.marksheet',$result['id']) }}" target="_blank">Print Marksheet</a><form method="post" action="{{ route('admin.results.destroy',$result['id']) }}" onsubmit="return confirm('Delete this result permanently?')">@csrf @method('DELETE')<button>Delete</button></form></div>
</article>@endforeach</div>
@else<div class="empty"><b>No examination results found</b><p>बाईं ओर form से पहला result publish करें।</p></div>@endif
</section>
</div>
@endsection
