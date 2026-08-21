@extends('admin.layout')
@section('title','Job Opportunities')
@section('content')
<div class="job-admin-grid">
<section class="panel job-compose"><div class="panel-title"><div><small>ADD VERIFIED OPENING</small><h2>Publish a job opportunity</h2></div></div>
<p class="section-note">केवल trusted company या verified job portal की opening publish करें।</p>
<form class="form-grid" method="post" action="{{ route('admin.jobs.store') }}">@csrf
<label>Job Role<input name="title" value="{{ old('title') }}" required maxlength="140"></label>
<label>Company / Organization<input name="company" value="{{ old('company') }}" required maxlength="140"></label>
<label>Location<input name="location" value="{{ old('location','Bihar Sharif') }}" required maxlength="140"></label>
<label>Job Type<select name="job_type"><option>Full Time</option><option>Part Time</option><option>Internship</option><option>Apprenticeship</option><option>Work From Home</option></select></label>
<label>Qualification<input name="qualification" value="{{ old('qualification') }}" maxlength="180" placeholder="12th / Graduate / DCA"></label>
<label>Salary / Stipend<input name="salary" value="{{ old('salary') }}" maxlength="100" placeholder="₹12,000–₹18,000"></label>
<label>Posted Date<input type="date" name="posted_at" value="{{ old('posted_at',date('Y-m-d')) }}" required></label>
<label>Last Apply Date<input type="date" name="deadline" value="{{ old('deadline') }}"></label>
<label class="full">Official Apply Link<input type="url" name="apply_url" value="{{ old('apply_url') }}" required placeholder="https://..."></label>
<label class="full">Job Details<textarea name="description" rows="4" maxlength="1200">{{ old('description') }}</textarea></label>
<label class="full check-label"><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified',true))> Mark as verified opening</label>
<div class="full"><button class="btn">Publish Job</button></div>
</form></section>
<section class="panel job-safety"><div class="panel-title"><div><small>STUDENT SAFETY</small><h2>Verification checklist</h2></div></div>
<div class="gallery-tips"><div><span>✓</span><p><b>Official link</b><br>Company career page या trusted portal का link दें।</p></div><div><span>₹</span><p><b>No payment</b><br>Interview/job के लिए fee माँगने वाली vacancy न डालें।</p></div><div><span>◷</span><p><b>Deadline</b><br>Last date के बाद job अपने-आप hide हो जाएगी।</p></div></div></section>
</div>
<section class="panel"><div class="panel-title"><div><small>JOB BOARD</small><h2>{{ count($jobs) }} opportunities</h2></div><a href="{{ route('home') }}#opportunities" target="_blank">View student job board ↗</a></div>
@if(count($jobs))<div class="job-admin-list">@foreach($jobs as $job)
<article><div class="job-company-icon">{{ strtoupper(substr($job['company'],0,1)) }}</div><div class="job-admin-copy"><div><span>{{ $job['job_type'] }}</span>@if($job['is_verified'])<b>✓ VERIFIED</b>@endif</div><h3>{{ $job['title'] }}</h3><h4>{{ $job['company'] }} · {{ $job['location'] }}</h4><p>{{ $job['description'] ?: 'No additional details.' }}</p><small>{{ $job['qualification'] ?: 'Qualification not specified' }} @if($job['salary']) · {{ $job['salary'] }}@endif @if($job['deadline']) · Apply by {{ CarbonCarbon::parse($job['deadline'])->format('d M Y') }}@endif</small></div>
<div class="notice-admin-actions"><em class="{{ $job['is_active'] ? 'on' : 'off' }}">{{ $job['is_active'] ? 'PUBLISHED' : 'HIDDEN' }}</em><a class="soft-btn" href="{{ $job['apply_url'] }}" target="_blank" rel="noopener">Check Link ↗</a><form method="post" action="{{ route('admin.jobs.toggle',$job['id']) }}">@csrf @method('PATCH')<button class="soft-btn">{{ $job['is_active'] ? 'Hide' : 'Publish' }}</button></form><form method="post" action="{{ route('admin.jobs.destroy',$job['id']) }}" onsubmit="return confirm('Delete this job permanently?')">@csrf @method('DELETE')<button class="soft-btn danger">Delete</button></form></div></article>
@endforeach</div>@else<div class="empty"><b>No job opportunities yet</b><p>पहली verified vacancy ऊपर दिए गए form से publish करें।</p></div>@endif
</section>
@endsection
