@extends('admin.layout')
@section('title','Certificates')
@section('content')
<div class="cards certificate-summary">
<div class="card blue"><small>Total Certificates</small><strong>{{ count($certificates) }}</strong></div><div class="card green"><small>Course Completion</small><strong>{{ $typeCounts['completion'] ?? 0 }}</strong></div><div class="card purple"><small>Merit</small><strong>{{ $typeCounts['merit'] ?? 0 }}</strong></div><div class="card orange"><small>Participation</small><strong>{{ $typeCounts['participation'] ?? 0 }}</strong></div>
</div>
<div class="certificate-admin-grid">
<section class="panel certificate-entry"><div class="panel-title"><div><small>ISSUE NEW CERTIFICATE</small><h2>Student certificate</h2></div></div>
<form method="post" action="{{ route('admin.certificates.store') }}">@csrf
<div class="form-grid">
<label>Student<select name="student_id" required><option value="">Select admitted student</option>@foreach($students as $student)<option value="{{ $student['id'] }}" @selected(old('student_id')===$student['id'])>{{ $student['student_name'] }} · {{ $student['course_code'] }} · {{ $student['roll_no'] ?? $student['application_no'] }}</option>@endforeach</select></label>
<label>Certificate Type<select name="type" required><option value="completion">Course Completion</option><option value="merit">Merit Certificate</option><option value="participation">Participation Certificate</option></select></label>
<label>Certificate Title<input name="title" value="{{ old('title','Certificate of Course Completion') }}" maxlength="150" required></label>
<label>Issue Date<input type="date" name="issue_date" value="{{ old('issue_date',now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></label>
<label>Completion Date<input type="date" name="completion_date" value="{{ old('completion_date') }}" max="{{ now()->toDateString() }}"></label>
<label>Grade / Distinction<input name="grade" value="{{ old('grade') }}" maxlength="20" placeholder="A / Distinction"></label>
</div><label class="certificate-description">Description<textarea name="description" rows="3" maxlength="500" placeholder="Optional achievement or course description">{{ old('description') }}</textarea></label>
<button class="btn">Issue Certificate</button></form></section>
<section class="panel"><form class="certificate-filter" method="get"><input name="search" value="{{ request('search') }}" placeholder="Student, roll no, certificate or verification code..."><select name="type"><option value="">All Types</option><option value="completion" @selected(request('type')==='completion')>Completion</option><option value="merit" @selected(request('type')==='merit')>Merit</option><option value="participation" @selected(request('type')==='participation')>Participation</option></select><button class="soft-btn">Search</button><a href="{{ route('admin.certificates.index') }}">Clear</a></form>
@if(count($certificates))<div class="certificate-records">@foreach($certificates as $certificate)<article><div class="certificate-icon">◇</div><div><small>{{ $certificate['certificate_no'] }}</small><h3>{{ $certificate['student']['student_name'] }}</h3><p>{{ ucwords(str_replace('_',' ',$certificate['type'])) }} · {{ $certificate['title'] }}</p><b>{{ $certificate['verification_code'] }}</b></div><div class="certificate-actions"><a href="{{ route('admin.certificates.print',$certificate['id']) }}" target="_blank">Print Certificate</a><a href="{{ route('certificates.verify',['code'=>$certificate['verification_code']]) }}" target="_blank">Verify Publicly</a><form method="post" action="{{ route('admin.certificates.destroy',$certificate['id']) }}" onsubmit="return confirm('Revoke this certificate permanently?')">@csrf @method('DELETE')<button>Revoke</button></form></div></article>@endforeach</div>
@else<div class="empty"><b>No certificates issued</b><p>बाईं ओर form से पहला certificate जारी करें।</p></div>@endif
</section></div>
@endsection
