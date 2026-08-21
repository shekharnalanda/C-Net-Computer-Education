@extends('admin.layout')
@section('title','Enquiry Management')
@section('content')
<div class="cards enquiry-summary">
    <a class="card blue" href="{{ route('admin.enquiries.index') }}"><span>All Enquiries</span><strong>{{ $totalCount }}</strong></a>
    <a class="card orange" href="{{ route('admin.enquiries.index',['status'=>'new']) }}"><span>New</span><strong>{{ $newCount }}</strong></a>
    <a class="card purple" href="{{ route('admin.enquiries.index',['status'=>'contacted']) }}"><span>Contacted</span><strong>{{ $contactedCount }}</strong></a>
    <a class="card green" href="{{ route('admin.enquiries.index',['status'=>'closed']) }}"><span>Closed</span><strong>{{ $closedCount }}</strong></a>
</div>

<div class="panel">
    <div class="panel-title"><div><small>FILTER & SEARCH</small><h2>Find Enquiries</h2></div><a class="export-link" href="{{ route('admin.enquiries.export', request()->query()) }}">Download CSV ↓</a></div>
    <form class="filter-grid" method="get">
        <label class="wide">Search<input name="q" value="{{ request('q') }}" placeholder="Name, phone, email, city or course"></label>
        <label>Status<select name="status"><option value="">All statuses</option>@foreach(['new'=>'New','contacted'=>'Contacted','closed'=>'Closed'] as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach</select></label>
        <label>Course<select name="course"><option value="">All courses</option>@foreach($courses as $course)<option value="{{ $course }}" @selected(request('course')===$course)>{{ $course }}</option>@endforeach</select></label>
        <label>From<input type="date" name="from" value="{{ request('from') }}"></label>
        <label>To<input type="date" name="to" value="{{ request('to') }}"></label>
        <div class="filter-actions"><button class="btn">Apply Filters</button><a href="{{ route('admin.enquiries.index') }}">Clear</a></div>
    </form>
</div>

<div class="panel">
    <div class="panel-title"><div><small>STUDENT LEADS</small><h2>{{ $enquiries->total() }} Results</h2></div></div>
    <table class="enquiry-table"><thead><tr><th>Student</th><th>Contact</th><th>Course & Message</th><th>Status</th><th>Received</th><th>Actions</th></tr></thead><tbody>
    @forelse($enquiries as $enquiry)
    @php($whatsapp = preg_replace('/D+/', '', $enquiry->phone))
    <tr>
        <td><b>{{ $enquiry->name }}</b><br><small>{{ $enquiry->city ?: 'City not provided' }}</small></td>
        <td><a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a><br>@if($enquiry->email)<a class="muted-link" href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>@endif</td>
        <td><span class="course-tag">{{ $enquiry->course_code ?: 'General enquiry' }}</span>@if($enquiry->message)<p class="message-preview">{{ $enquiry->message }}</p>@endif</td>
        <td><form method="post" action="{{ route('admin.enquiries.status',$enquiry) }}">@csrf @method('PATCH')<select class="status-select status-{{ $enquiry->status }}" name="status" onchange="this.form.submit()">@foreach(['new','contacted','closed'] as $status)<option value="{{ $status }}" @selected($enquiry->status===$status)>{{ ucfirst($status) }}</option>@endforeach</select></form></td>
        <td>{{ $enquiry->created_at->format('d M Y') }}<br><small>{{ $enquiry->created_at->format('h:i A') }}</small></td>
        <td><div class="row-actions"><a class="icon-action whatsapp" href="https://wa.me/{{ $whatsapp }}" target="_blank" title="WhatsApp">WA</a><a class="icon-action" href="tel:{{ $enquiry->phone }}" title="Call">Call</a><form method="post" action="{{ route('admin.enquiries.destroy',$enquiry) }}" onsubmit="return confirm('Delete this enquiry permanently?')">@csrf @method('DELETE')<button class="icon-action delete" title="Delete">×</button></form></div></td>
    </tr>
    @empty
    <tr><td colspan="6"><div class="empty"><b>No enquiries match these filters</b><p>Clear filters or wait for a new website enquiry.</p><a href="{{ route('admin.enquiries.index') }}">Clear all filters</a></div></td></tr>
    @endforelse
    </tbody></table>
    <div class="pagination">{{ $enquiries->links() }}</div>
</div>
@endsection
