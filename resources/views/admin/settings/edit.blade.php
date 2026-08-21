@extends('admin.layout')
@section('title','Website Settings')
@section('content')
<div class="settings-intro"><div><span>⚙</span><div><h2>Manage website information</h2><p>Saved changes automatically appear on the public homepage.</p></div></div><a href="{{ route('home') }}" target="_blank">Preview Website ↗</a></div>
<form method="post" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
<div class="settings-grid">
<section class="panel settings-panel"><div class="panel-title"><div><small>CONTACT DETAILS</small><h2>Phone & Email</h2></div></div><div class="settings-form">
<label>Display Phone<input name="phone" value="{{ old('phone',$settings['phone']) }}" required></label>
<label>WhatsApp Number<input name="whatsapp" value="{{ old('whatsapp',$settings['whatsapp']) }}" required><small>Digits with country code, e.g. 917004773247</small></label>
<label class="full">Enquiry Email<input type="email" name="email" value="{{ old('email',$settings['email']) }}" required></label>
</div></section>
<section class="panel settings-panel"><div class="panel-title"><div><small>INSTITUTE ADDRESS</small><h2>Location</h2></div></div><div class="settings-form">
<label class="full">Address<input name="address_line" value="{{ old('address_line',$settings['address_line']) }}" required></label>
<label>City<input name="city" value="{{ old('city',$settings['city']) }}" required></label><label>District<input name="district" value="{{ old('district',$settings['district']) }}" required></label>
<label>State<input name="state" value="{{ old('state',$settings['state']) }}" required></label><label>PIN<input name="pin" value="{{ old('pin',$settings['pin']) }}" maxlength="6" required></label>
</div></section>
<section class="panel settings-panel full-panel"><div class="panel-title"><div><small>JOB SEARCH DEFAULTS</small><h2>Student Job Search</h2></div></div><div class="settings-form">
<label>Default Job Role<input name="job_role" value="{{ old('job_role',$settings['job_role']) }}" required></label><label>Default Location<input name="job_location" value="{{ old('job_location',$settings['job_location']) }}" required></label>
</div></section></div>
<div class="settings-save"><div><b>Publish these details</b><p>Save and refresh the homepage to view changes.</p></div><button class="btn">Save Website Settings</button></div></form>
@endsection
