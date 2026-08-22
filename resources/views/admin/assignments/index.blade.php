@extends('admin.layout')
@section('title','Assignment Review')
@section('content')
<div class="cards assignment-summary">
<div class="card blue"><small>Total Submissions</small><strong>{{ count($submissions) }}</strong></div>
<div class="card orange"><small>Awaiting Review</small><strong>{{ $pending }}</strong></div>
<div class="card green"><small>Reviewed</small><strong>{{ $reviewed }}</strong></div>
<div class="card purple"><small>Courses</small><strong>{{ $courses->count() }}</strong></div>
</div>
<section class="panel assignment-panel">
<form class="assignment-filter" method="get"><input name="search" value="{{ request('search') }}" placeholder="Student, application no. or assignment..."><select name="course"><option value="">All Courses</option>@foreach($courses as $course)<option @selected(request('course')===$course)>{{ $course }}</option>@endforeach</select><select name="status"><option value="">All Status</option><option value="submitted" @selected(request('status')==='submitted')>Awaiting Review</option><option value="reviewed" @selected(request('status')==='reviewed')>Reviewed</option></select><button class="soft-btn">Search</button><a href="{{ route('admin.assignments.index') }}">Clear</a></form>
@if(count($submissions))<div class="assignment-records">@foreach($submissions as $submission)<article>
<div class="assignment-status status-{{ $submission['status'] }}"><span>{{ $submission['status']==='reviewed' ? ($submission['marks'].'%') : 'NEW' }}</span><small>{{ $submission['status']==='reviewed' ? 'Reviewed' : 'Pending' }}</small></div>
<div class="assignment-details"><small>{{ $submission['student']['application_no'] ?? 'Student' }} · {{ $submission['course_code'] }}</small><h3>{{ $submission['resource']['title'] ?? 'Assignment' }}</h3><b>{{ $submission['student']['student_name'] ?? 'Unknown student' }}</b><p>{{ $submission['answer_text'] ?: 'No written answer; link submitted.' }}</p>@if($submission['submission_url'])<a href="{{ $submission['submission_url'] }}" target="_blank" rel="noopener noreferrer">Open Submitted Work ↗</a>@endif<time>Submitted {{ \Carbon\Carbon::parse($submission['submitted_at'])->format('d M Y, h:i A') }}</time></div>
<form class="assignment-review-form" method="post" action="{{ route('admin.assignments.review',$submission['id']) }}">@csrf @method('PATCH')<label>Marks / 100<input type="number" name="marks" min="0" max="100" step="0.01" value="{{ $submission['marks'] ?? '' }}" required></label><label>Feedback<textarea name="feedback" rows="2" maxlength="1000" placeholder="Feedback for student">{{ $submission['feedback'] ?? '' }}</textarea></label><button class="btn">{{ $submission['status']==='reviewed' ? 'Update Review' : 'Save Review' }}</button></form>
<form method="post" action="{{ route('admin.assignments.destroy',$submission['id']) }}" onsubmit="return confirm('Delete this submission?')">@csrf @method('DELETE')<button class="assignment-delete">Delete</button></form>
</article>@endforeach</div>
@else<div class="empty"><b>No assignment submissions found</b><p>Student submissions will appear here automatically.</p></div>@endif
</section>
@endsection
