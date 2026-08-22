@extends('admin.layout')
@section('title','Students Register')
@section('content')
<div class="cards student-summary">
    <div class="card blue"><small>Admitted Students</small><strong>{{ $studentCount }}</strong></div>
    <div class="card green"><small>Fees Collected</small><strong>₹{{ number_format($totalPaid,2) }}</strong></div>
    <div class="card orange"><small>Outstanding Balance</small><strong>₹{{ number_format($totalBalance,2) }}</strong></div>
</div>
<section class="panel">
<form class="student-filter" method="get">
    <input name="search" value="{{ request('search') }}" placeholder="Search application no, student, guardian or phone">
    <select name="course"><option value="">All courses</option>@foreach($courses as $course)<option value="{{ $course->code }}" @selected(request('course')===$course->code)>{{ $course->code }} — {{ $course->title }}</option>@endforeach</select>
    <select name="payment_status"><option value="">All fee statuses</option>@foreach(['unpaid','partial','paid'] as $status)<option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select>
    <button class="btn">Search</button><a href="{{ route('admin.students.index') }}">Clear</a>
</form>
</section>
<section class="panel"><div class="panel-title"><div><small>ADMITTED STUDENT RECORDS</small><h2>{{ $studentCount }} students</h2></div><a href="{{ route('admin.admissions.index') }}">Admission workspace →</a></div>
@if(count($students))
<div class="student-table-wrap"><table class="student-table"><thead><tr><th>Student</th><th>Application</th><th>Course</th><th>Contact</th><th>Fee Status</th><th>Balance</th><th>Actions</th></tr></thead><tbody>
@foreach($students as $student)<tr>
<td><b>{{ $student['student_name'] }}</b><small>{{ $student['guardian_name'] }} · {{ $student['city'] }}</small></td>
<td><b>{{ $student['application_no'] }}</b><small>{{ \Carbon\Carbon::parse($student['created_at'])->format('d M Y') }}</small></td>
<td><span class="course-tag">{{ $student['course_code'] }}</span><small>{{ $student['preferred_time'] ?: 'Batch not set' }}</small></td>
<td><a href="tel:{{ preg_replace('/\s+/', '', $student['phone']) }}">{{ $student['phone'] }}</a></td>
<td><span class="student-payment payment-{{ $student['payment_status'] }}">{{ ucfirst($student['payment_status']) }}</span><small>Paid ₹{{ number_format((float)$student['paid_amount'],2) }}</small></td>
<td><b>₹{{ number_format((float)$student['balance_amount'],2) }}</b></td>
<td><div class="student-actions"><a href="{{ route('admin.students.card',$student['id']) }}" target="_blank">Student Card</a><a href="{{ route('admin.admissions.receipt',$student['id']) }}" target="_blank">Fee Receipt</a></div></td>
</tr>@endforeach
</tbody></table></div>
@else<div class="empty"><b>No admitted students found</b><p>Admission status को “Admitted” करने के बाद student यहाँ दिखाई देगा।</p><a href="{{ route('admin.admissions.index') }}">Open Admissions →</a></div>@endif
</section>
@endsection
