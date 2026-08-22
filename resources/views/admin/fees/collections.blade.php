@extends('admin.layout')
@section('title','Fee Collection Report')
@section('content')
<div class="cards collection-summary">
    <div class="card blue"><small>Total Collected</small><strong>₹{{ number_format($totalCollected,2) }}</strong></div>
    <div class="card green"><small>Transactions</small><strong>{{ $transactionCount }}</strong></div>
    <div class="card orange"><small>Cash Collection</small><strong>₹{{ number_format($cashCollected,2) }}</strong></div>
    <div class="card purple"><small>Digital Collection</small><strong>₹{{ number_format($digitalCollected,2) }}</strong></div>
</div>

<section class="panel">
<form class="collection-filter" method="get">
    <label>From<input type="date" name="from" value="{{ request('from',$from) }}" max="{{ now()->toDateString() }}"></label>
    <label>To<input type="date" name="to" value="{{ request('to',$to) }}" max="{{ now()->toDateString() }}"></label>
    <input name="search" value="{{ request('search') }}" placeholder="Student, receipt, application or reference...">
    <select name="course"><option value="">All Courses</option>@foreach($courses as $course)<option value="{{ $course->code }}" @selected(request('course')===$course->code)>{{ $course->code }} — {{ $course->title }}</option>@endforeach</select>
    <select name="mode"><option value="">All Modes</option>@foreach(['cash'=>'Cash','upi'=>'UPI','bank'=>'Bank Transfer','card'=>'Card','other'=>'Other'] as $value=>$label)<option value="{{ $value }}" @selected(request('mode')===$value)>{{ $label }}</option>@endforeach</select>
    <button class="btn">Generate</button><a href="{{ route('admin.fees.collections') }}">Current Month</a>
    <a class="export-link" href="{{ route('admin.fees.collections.export',request()->query()) }}">Export CSV ↓</a>
</form>
</section>

@if($dailyTotals->count())
<section class="panel"><div class="panel-title"><div><small>DAILY COLLECTION SUMMARY</small><h2>{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</h2></div></div>
<div class="daily-collection-grid">@foreach($dailyTotals as $date=>$amount)<div><time>{{ \Carbon\Carbon::parse($date)->format('d M') }}</time><strong>₹{{ number_format($amount,2) }}</strong></div>@endforeach</div>
</section>
@endif

<section class="panel"><div class="panel-title"><div><small>PAYMENT TRANSACTIONS</small><h2>{{ $transactionCount }} collection records</h2></div><a href="{{ route('admin.admissions.index') }}">Record New Payment →</a></div>
@if(count($transactions))
<div class="collection-table-wrap"><table class="collection-table"><thead><tr><th>Date / Receipt</th><th>Student</th><th>Course</th><th>Mode</th><th>Reference / Note</th><th>Amount</th><th>Receipt</th></tr></thead><tbody>
@foreach($transactions as $row)<tr>
<td><b>{{ \Carbon\Carbon::parse($row['payment_date'])->format('d M Y') }}</b><small>{{ $row['receipt_no'] }}</small></td>
<td><b>{{ $row['student_name'] }}</b><small>{{ $row['application_no'] }}@if($row['roll_no']) · {{ $row['roll_no'] }}@endif</small></td>
<td><span class="collection-course">{{ $row['course_code'] }}</span></td>
<td><span class="collection-mode mode-{{ $row['mode'] }}">{{ strtoupper($row['mode']) }}</span></td>
<td>{{ $row['reference'] ?: '—' }}<small>{{ $row['note'] ?: '' }}</small></td>
<td><strong class="collection-amount">₹{{ number_format((float)$row['amount'],2) }}</strong></td>
<td><a class="receipt-action" href="{{ route('admin.admissions.payments.receipt',[$row['student_id'],$row['id']]) }}" target="_blank">Print</a></td>
</tr>@endforeach
</tbody></table></div>
@else<div class="empty"><b>No payment transactions found</b><p>Date range या filters बदलकर दोबारा देखें। Legacy opening balances transaction report में शामिल नहीं होते।</p></div>@endif
</section>
@endsection
