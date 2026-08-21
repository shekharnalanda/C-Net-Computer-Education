@extends('admin.layout')
@section('title','Gallery Management')
@section('content')
<div class="gallery-admin-grid">
<section class="panel upload-panel">
<div class="panel-title"><div><small>ADD NEW PHOTO</small><h2>Upload gallery image</h2></div></div>
<p class="section-note">Computer lab, classroom, student activity या event की JPG, PNG अथवा WebP image लगाएँ। अधिकतम आकार 5 MB है।</p>
<form class="profile-form" method="post" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">@csrf
<label>Photo Title<input name="title" value="{{ old('title') }}" required maxlength="100"></label>
<label>Short Caption<textarea name="caption" rows="3" maxlength="300">{{ old('caption') }}</textarea></label>
<label class="gallery-file">Choose Image<input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required></label>
<button class="btn">Upload & Publish</button>
</form>
</section>
<section class="panel gallery-help">
<div class="panel-title"><div><small>GALLERY STATUS</small><h2>{{ count($items) }} photos</h2></div></div>
<div class="gallery-tips"><div><span>✓</span><p><b>Automatic display</b><br>Published photos appear on the homepage.</p></div><div><span>◉</span><p><b>Visibility control</b><br>Hide a photo without deleting it.</p></div><div><span>⌕</span><p><b>Large preview</b><br>Visitors can open every photo in a lightbox.</p></div></div>
</section>
</div>
<section class="panel"><div class="panel-title"><div><small>PHOTO LIBRARY</small><h2>Manage uploaded photos</h2></div><a href="{{ route('home') }}#gallery" target="_blank">View public gallery ↗</a></div>
@if(count($items))
<div class="gallery-admin-cards">
@foreach($items as $item)
<article class="gallery-admin-card">
<img src="{{ asset($item['path']) }}" alt="{{ $item['title'] }}">
<div><div class="gallery-card-title"><h3>{{ $item['title'] }}</h3><em class="{{ $item['is_active'] ? 'on' : 'off' }}">{{ $item['is_active'] ? 'PUBLISHED' : 'HIDDEN' }}</em></div>
<p>{{ $item['caption'] ?: 'No caption added.' }}</p>
<div class="course-card-actions">
<form method="post" action="{{ route('admin.gallery.toggle',$item['id']) }}">@csrf @method('PATCH')<button class="soft-btn">{{ $item['is_active'] ? 'Hide Photo' : 'Publish Photo' }}</button></form>
<form method="post" action="{{ route('admin.gallery.destroy',$item['id']) }}" onsubmit="return confirm('Delete this photo permanently?')">@csrf @method('DELETE')<button class="soft-btn danger">Delete</button></form>
</div></div></article>
@endforeach
</div>
@else
<div class="empty"><b>No gallery photos yet</b><p>पहली photo ऊपर दिए गए upload form से जोड़ें।</p></div>
@endif
</section>
@endsection
