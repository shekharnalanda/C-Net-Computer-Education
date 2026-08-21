@extends('admin.layout')
@section('title','Notices & Announcements')
@section('content')
<div class="notice-admin-grid">
<section class="panel notice-compose"><div class="panel-title"><div><small>CREATE ANNOUNCEMENT</small><h2>Publish a new notice</h2></div></div>
<form class="form-grid" method="post" action="{{ route('admin.notices.store') }}">@csrf
<label class="full">English Title<input name="title" value="{{ old('title') }}" required maxlength="140"></label>
<label class="full">Hindi Title / हिंदी शीर्षक<input name="title_hi" value="{{ old('title_hi') }}" maxlength="140"></label>
<label>Notice Type<select name="type" required><option value="admission">Admission</option><option value="course">Course Update</option><option value="event">Event</option><option value="holiday">Holiday</option><option value="important">Important</option><option value="general">General</option></select></label>
<label>Notice Date<input type="date" name="notice_date" value="{{ old('notice_date',date('Y-m-d')) }}" required></label>
<label>Expiry Date<input type="date" name="expires_at" value="{{ old('expires_at') }}"><small>Optional—notice hides automatically after this date.</small></label>
<label>Related Link<input type="url" name="link" value="{{ old('link') }}" placeholder="https://..."></label>
<label class="full">Details / विवरण<textarea name="description" rows="4" maxlength="800">{{ old('description') }}</textarea></label>
<label class="full check-label"><input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned'))> Pin this notice at the top</label>
<div class="full"><button class="btn">Publish Notice</button></div>
</form></section>
<section class="panel notice-guide"><div class="panel-title"><div><small>SMART DISPLAY</small><h2>How notices work</h2></div></div>
<div class="gallery-tips"><div><span>↥</span><p><b>Pinned first</b><br>Important notices stay at the top.</p></div><div><span>◷</span><p><b>Automatic expiry</b><br>Expired notices disappear from the homepage.</p></div><div><span>अ</span><p><b>Bilingual</b><br>English और Hindi दोनों शीर्षक दिखते हैं।</p></div></div></section>
</div>
<section class="panel"><div class="panel-title"><div><small>NOTICE BOARD</small><h2>{{ count($notices) }} announcements</h2></div><a href="{{ route('home') }}#notices" target="_blank">View public notices ↗</a></div>
@if(count($notices))
<div class="notice-admin-list">@foreach($notices as $notice)
<article><div class="notice-admin-date"><b>{{ CarbonCarbon::parse($notice['notice_date'])->format('d') }}</b><span>{{ CarbonCarbon::parse($notice['notice_date'])->format('M Y') }}</span></div>
<div class="notice-admin-copy"><div><span class="notice-type type-{{ $notice['type'] }}">{{ strtoupper($notice['type']) }}</span>@if($notice['is_pinned'])<span class="notice-pin">PINNED</span>@endif</div><h3>{{ $notice['title'] }}</h3>@if($notice['title_hi'])<h4>{{ $notice['title_hi'] }}</h4>@endif<p>{{ $notice['description'] ?: 'No additional details.' }}</p>@if($notice['expires_at'])<small>Expires: {{ CarbonCarbon::parse($notice['expires_at'])->format('d M Y') }}</small>@endif</div>
<div class="notice-admin-actions"><em class="{{ $notice['is_active'] ? 'on' : 'off' }}">{{ $notice['is_active'] ? 'PUBLISHED' : 'HIDDEN' }}</em><form method="post" action="{{ route('admin.notices.toggle',$notice['id']) }}">@csrf @method('PATCH')<button class="soft-btn">{{ $notice['is_active'] ? 'Hide' : 'Publish' }}</button></form><form method="post" action="{{ route('admin.notices.destroy',$notice['id']) }}" onsubmit="return confirm('Delete this notice permanently?')">@csrf @method('DELETE')<button class="soft-btn danger">Delete</button></form></div>
</article>@endforeach</div>
@else<div class="empty"><b>No notices yet</b><p>पहला announcement ऊपर दिए गए form से publish करें।</p></div>@endif
</section>
@endsection
