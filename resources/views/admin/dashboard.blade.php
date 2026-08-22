@extends('admin.layout')
@section('title','Dashboard')
@section('content')
<div class="quick-actions">
    <a href="{{ route('admin.courses.index') }}"><span>＋</span><div><b>Add or Edit Course</b><small>Manage website course details</small></div></a>
    <a href="{{ route('admin.enquiries.index') }}"><span>✉</span><div><b>View Enquiries</b><small>Contact prospective students</small></div></a>
    <a href="{{ route('admin.students.index') }}"><span>♟</span><div><b>Students Register</b><small>Admitted students and fee balances</small></div></a>
    <a href="{{ route('home') }}" target="_blank"><span>↗</span><div><b>Open Website</b><small>Check the public home page</small></div></a>
</div>

<div class="cards dashboard-cards">
    <div class="card blue"><span>Total Courses</span><strong>{{ $courseCount }}</strong><small>{{ $activeCourseCount }} active · {{ $hiddenCourseCount }} hidden</small></div>
    <div class="card green"><span>Total Enquiries</span><strong>{{ $enquiryCount }}</strong><small>{{ $monthEnquiryCount }} received this month</small></div>
    <div class="card orange"><span>New Enquiries</span><strong>{{ $newEnquiryCount }}</strong><small>Need your attention</small></div>
    <div class="card purple"><span>Contacted</span><strong>{{ $contactedCount }}</strong><small>Follow-up in progress</small></div>
    <div class="card dark"><span>Closed</span><strong>{{ $closedCount }}</strong><small>{{ $conversionRate }}% closure rate</small></div>
    <div class="card cyan"><span>Today</span><strong>{{ $todayEnquiryCount }}</strong><small>Enquiries received today</small></div>
</div>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-title"><div><small>ADMISSION PIPELINE</small><h2>Recent Enquiries</h2></div><a href="{{ route('admin.enquiries.index') }}">View all →</a></div>
        <table><thead><tr><th>Student</th><th>Phone</th><th>Course</th><th>Status</th><th>Date</th></tr></thead><tbody>
        @forelse($recentEnquiries as $enquiry)
        <tr><td><b>{{ $enquiry->name }}</b><br><small>{{ $enquiry->city }}</small></td><td><a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a></td><td>{{ $enquiry->course_code ?: 'Not selected' }}</td><td><span class="badge status-{{ $enquiry->status }}">{{ ucfirst($enquiry->status) }}</span></td><td>{{ $enquiry->created_at->format('d M Y') }}</td></tr>
        @empty
        <tr><td colspan="5"><div class="empty"><b>No enquiries yet</b><p>New submissions from the website enquiry form will appear here automatically.</p><a href="{{ route('home') }}#enquiry" target="_blank">Open enquiry form ↗</a></div></td></tr>
        @endforelse
        </tbody></table>
    </section>

    <section class="panel">
        <div class="panel-title"><div><small>WEBSITE CATALOGUE</small><h2>Courses</h2></div><a href="{{ route('admin.courses.index') }}">Manage →</a></div>
        <div class="course-list">
        @forelse($recentCourses as $course)
            <a href="{{ route('admin.courses.index') }}"><span class="course-code">{{ $course->code }}</span><div><b>{{ $course->title }}</b><small>{{ $course->duration }} · {{ $course->level }}</small></div><em class="{{ $course->is_active ? 'on' : 'off' }}">{{ $course->is_active ? 'Active' : 'Hidden' }}</em></a>
        @empty
            <div class="empty"><b>No courses available</b><p>Add your first computer course to show it on the website.</p><a href="{{ route('admin.courses.index') }}">Add course →</a></div>
        @endforelse
        </div>
    </section>
</div>
@endsection
