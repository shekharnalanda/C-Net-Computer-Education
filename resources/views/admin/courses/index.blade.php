@extends('admin.layout')
@section('title','Course Management')
@section('content')
<div class="cards course-summary">
    <a class="card blue" href="{{ route('admin.courses.index') }}"><span>All Courses</span><strong>{{ $totalCount }}</strong></a>
    <a class="card green" href="{{ route('admin.courses.index',['status'=>'active']) }}"><span>Active on Website</span><strong>{{ $activeCount }}</strong></a>
    <a class="card dark" href="{{ route('admin.courses.index',['status'=>'hidden']) }}"><span>Hidden Courses</span><strong>{{ $hiddenCount }}</strong></a>
</div>

<details class="panel add-course-panel" @if($errors->any()) open @endif>
    <summary><span>＋</span><div><b>Add New Course</b><small>Create complete English and Hindi course details</small></div></summary>
    <form class="form-grid course-form" method="post" action="{{ route('admin.courses.store') }}">@csrf
        <label>Course Code<input name="code" value="{{ old('code') }}" required placeholder="e.g. DCA"></label>
        <label>Duration<input name="duration" value="{{ old('duration') }}" required placeholder="e.g. 6 Months"></label>
        <label>Course Fee (₹)<input type="number" name="fee_amount" value="{{ old('fee_amount') }}" min="0" step="0.01" placeholder="e.g. 4500"></label>
        <label>Fee Note<input name="fee_note" value="{{ old('fee_note') }}" maxlength="160" placeholder="e.g. Registration included / Monthly option"></label>
        <label class="full">English Title<input name="title" value="{{ old('title') }}" required></label>
        <label class="full">Hindi Title<input name="title_hi" value="{{ old('title_hi') }}"></label>
        <label>Category<select name="level" required>@foreach(['Foundation','Job-ready','Career','Technical','Creative','Future Skill'] as $level)<option @selected(old('level')===$level)>{{ $level }}</option>@endforeach</select></label>
        <label>Eligibility<input name="eligibility" value="{{ old('eligibility') }}"></label>
        <label class="full">Short Summary<textarea name="summary" required maxlength="500">{{ old('summary') }}</textarea></label>
        <label class="full">Modules — one per line<textarea name="modules_text">{{ old('modules_text') }}</textarea></label>
        <label class="full">Career Opportunities — one per line<textarea name="careers_text">{{ old('careers_text') }}</textarea></label>
        <label>Display Order<input type="number" name="sort_order" value="{{ old('sort_order', $totalCount + 1) }}" min="0"></label>
        <label class="check-label"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Show on website</label>
        <button class="btn full">Add Course</button>
    </form>
</details>

<div class="panel">
    <div class="panel-title"><div><small>FILTER & SEARCH</small><h2>Find Courses</h2></div><a href="{{ route('home') }}#courses" target="_blank">View website →</a></div>
    <form class="course-filter" method="get">
        <input name="q" value="{{ request('q') }}" placeholder="Search code, English/Hindi title or summary">
        <select name="status"><option value="">All status</option><option value="active" @selected(request('status')==='active')>Active</option><option value="hidden" @selected(request('status')==='hidden')>Hidden</option></select>
        <select name="level"><option value="">All categories</option>@foreach($levels as $level)<option value="{{ $level }}" @selected(request('level')===$level)>{{ $level }}</option>@endforeach</select>
        <button class="btn">Search</button><a href="{{ route('admin.courses.index') }}">Clear</a>
    </form>
</div>

<div class="course-admin-grid">
@forelse($courses as $course)
<article class="course-admin-card">
    <div class="course-card-head">
        <span class="course-code">{{ $course->code }}</span>
        <div><h3>{{ $course->title }}</h3><p>{{ $course->title_hi ?: 'Hindi title not added' }}</p></div>
        <em class="{{ $course->is_active ? 'on' : 'off' }}">{{ $course->is_active ? 'Active' : 'Hidden' }}</em>
    </div>
    <div class="course-meta"><span>{{ $course->duration }}</span><span>{{ $course->level }}</span><span>{{ $course->fee_amount !== null ? '₹'.number_format((float) $course->fee_amount, 2) : 'Fee not added' }}</span><span>Order {{ $course->sort_order }}</span></div>
    @if($course->fee_note)<p class="course-fee-note">{{ $course->fee_note }}</p>@endif
    <p class="course-summary-text">{{ $course->summary }}</p>
    <div class="course-card-actions">
        <form method="post" action="{{ route('admin.courses.toggle',$course) }}">@csrf @method('PATCH')<button class="soft-btn">{{ $course->is_active ? 'Hide from Website' : 'Publish on Website' }}</button></form>
        <button class="soft-btn edit-course" type="button" onclick="document.getElementById('edit-{{ $course->id }}').showModal()">Edit Details</button>
        <form method="post" action="{{ route('admin.courses.destroy',$course) }}" onsubmit="return confirm('Delete {{ $course->code }} permanently?')">@csrf @method('DELETE')<button class="soft-btn danger">Delete</button></form>
    </div>
</article>

<dialog class="course-edit-dialog" id="edit-{{ $course->id }}">
    <div class="dialog-head"><div><small>EDIT COURSE</small><h2>{{ $course->code }} — {{ $course->title }}</h2></div><button type="button" onclick="this.closest('dialog').close()">×</button></div>
    <form class="form-grid course-form" method="post" action="{{ route('admin.courses.update',$course) }}">@csrf @method('PUT')
        <label>Course Code<input name="code" value="{{ $course->code }}" required></label>
        <label>Duration<input name="duration" value="{{ $course->duration }}" required></label>
        <label>Course Fee (₹)<input type="number" name="fee_amount" value="{{ $course->fee_amount }}" min="0" step="0.01" placeholder="e.g. 4500"></label>
        <label>Fee Note<input name="fee_note" value="{{ $course->fee_note }}" maxlength="160" placeholder="Registration, instalment or discount information"></label>
        <label class="full">English Title<input name="title" value="{{ $course->title }}" required></label>
        <label class="full">Hindi Title<input name="title_hi" value="{{ $course->title_hi }}"></label>
        <label>Category<input name="level" value="{{ $course->level }}" required></label>
        <label>Eligibility<input name="eligibility" value="{{ $course->eligibility }}"></label>
        <label class="full">Summary<textarea name="summary" maxlength="500" required>{{ $course->summary }}</textarea></label>
        <label class="full">Modules — one per line<textarea name="modules_text">{{ implode("
", $course->modules ?: []) }}</textarea></label>
        <label class="full">Career Opportunities — one per line<textarea name="careers_text">{{ implode("
", $course->careers ?: []) }}</textarea></label>
        <label>Display Order<input type="number" name="sort_order" value="{{ $course->sort_order }}" min="0"></label>
        <label class="check-label"><input type="checkbox" name="is_active" value="1" @checked($course->is_active)> Show on website</label>
        <button class="btn full">Save Changes</button>
    </form>
</dialog>
@empty
<div class="panel empty course-empty"><b>No courses match these filters</b><p>Clear filters or add a new course.</p><a href="{{ route('admin.courses.index') }}">Clear filters</a></div>
@endforelse
</div>
<div class="pagination">{{ $courses->links() }}</div>
@endsection
