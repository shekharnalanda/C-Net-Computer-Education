@extends('admin.layout')
@section('title','Admission & Fee Records')
@section('content')
@php $counts=collect($allApplications)->countBy('status'); @endphp
<div class="cards admission-summary fee-summary">
    <div class="card blue"><small>Total Applications</small><strong>{{ count($allApplications) }}</strong></div>
    <div class="card orange"><small>Pending Review</small><strong>{{ $counts['pending'] ?? 0 }}</strong></div>
    <div class="card green"><small>Admitted Students</small><strong>{{ $counts['admitted'] ?? 0 }}</strong></div>
    <div class="card purple"><small>Total Course Fees</small><strong>₹{{ number_format($totalFees,2) }}</strong></div>
    <div class="card cyan"><small>Fees Collected</small><strong>₹{{ number_format($totalPaid,2) }}</strong></div>
    <div class="card dark"><small>Outstanding Balance</small><strong>₹{{ number_format($totalBalance,2) }}</strong></div>
</div>

<section class="panel"><form class="course-filter admission-filter" method="get">
    <input name="search" value="{{ request('search') }}" placeholder="Application no, student, guardian or phone...">
    <select name="status"><option value="">All Statuses</option>@foreach(['pending','contacted','verified','admitted','rejected'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select>
    <input name="course" value="{{ request('course') }}" placeholder="Course code">
    <button class="btn">Search</button><a href="{{ route('admin.admissions.index') }}">Clear</a>
    <a class="export-link" href="{{ route('admin.admissions.export') }}">Export Fee CSV ↓</a>
</form></section>

<section class="panel"><div class="panel-title"><div><small>STUDENT ADMISSION & FEE WORKSPACE</small><h2>{{ count($applications) }} records</h2></div><a href="{{ route('admission.create') }}" target="_blank">Open admission form ↗</a></div>
@if(count($applications))
<div class="admission-admin-list">@foreach($applications as $application)
<article>
    <div class="application-number"><small>APPLICATION</small><b>{{ $application['application_no'] }}</b><span>{{ \Carbon\Carbon::parse($application['created_at'])->format('d M Y, h:i A') }}</span>@if($application['receipt_no'] ?? null)<em>{{ $application['receipt_no'] }}</em>@endif</div>
    <div class="application-person">
        <h3>{{ $application['student_name'] }}</h3>
        <p>{{ $application['guardian_name'] }} · {{ $application['gender'] }} · DOB {{ \Carbon\Carbon::parse($application['dob'])->format('d M Y') }}</p>
        <div><span>{{ $application['course_code'] }}</span><span>{{ $application['qualification'] }}</span><span>{{ $application['city'] }}</span><span class="payment-{{ $application['payment_status'] }}">{{ ucfirst($application['payment_status']) }}</span></div>
        <details><summary>View full application</summary><p><b>Address:</b> {{ $application['address'] }}</p><p><b>Email:</b> {{ $application['email'] ?: '—' }} · <b>Preferred time:</b> {{ $application['preferred_time'] ?: '—' }}</p>@if($application['message'])<p><b>Message:</b> {{ $application['message'] }}</p>@endif</details>
        <form class="fee-record-form" method="post" action="{{ route('admin.admissions.payment',$application['id']) }}">@csrf @method('PATCH')
            <label>Course Fee ₹<input type="number" name="course_fee" min="0" step="0.01" value="{{ $application['course_fee'] }}" required></label>
            <label>Paid Amount ₹<input type="number" name="paid_amount" min="0" max="{{ $application['course_fee'] }}" step="0.01" value="{{ $application['paid_amount'] }}" required></label>
            <label>Balance ₹<input value="{{ number_format($application['balance_amount'],2) }}" readonly></label>
            <label>Payment Note<input name="payment_note" value="{{ $application['payment_note'] ?? '' }}" maxlength="255" placeholder="Cash / UPI / instalment"></label>
            <button class="soft-btn">Save Fee Record</button>
        </form>
    </div>
    <div class="application-contact"><a href="tel:{{ preg_replace('/\s+/', '', $application['phone']) }}">Call</a><a class="whatsapp" href="https://wa.me/{{ preg_replace('/\D+/', '', $application['phone']) }}" target="_blank">WhatsApp</a></div>
    <div class="application-actions">
        <form method="post" action="{{ route('admin.admissions.status',$application['id']) }}">@csrf @method('PATCH')<select name="status" class="status-select status-{{ $application['status'] }}" onchange="this.form.submit()">@foreach(['pending','contacted','verified','admitted','rejected'] as $status)<option value="{{ $status }}" @selected($application['status']===$status)>{{ ucfirst($status) }}</option>@endforeach</select></form>
        <form method="post" action="{{ route('admin.admissions.destroy',$application['id']) }}" onsubmit="return confirm('Delete this application permanently?')">@csrf @method('DELETE')<button class="soft-btn danger">Delete</button></form>
    </div>
</article>
@endforeach</div>
@else<div class="empty"><b>No matching applications</b><p>Search filters बदलें या public admission form share करें।</p></div>@endif
</section>
@endsection
