@extends('admin.layout')
@section('title','Demo Data Mode')
@section('content')
<div class="cards"><div class="card blue"><small>Demo Students</small><strong>{{ $status['students'] }}</strong></div><div class="card purple"><small>Test Attempts</small><strong>{{ $status['attempts'] }}</strong></div><div class="card green"><small>Final Marksheets</small><strong>{{ $status['results'] }}</strong></div><div class="card orange"><small>Sample Certificates</small><strong>{{ $status['certificates'] }}</strong></div></div>
<section class="panel"><div class="panel-title"><div><small>SAFE SHOWCASE DATA</small><h2>12 courses × 5 demo students</h2></div></div><p>Demo students, completed assessments, marksheets और certificates केवल system demonstration के लिए हैं। Certificates पर SAMPLE/DEMO पहचान रहेगी और demo fee records official collection reports में शामिल नहीं होंगे।</p>
@if($status['students']<60)<form method="post" action="{{ route('admin.demo-data.install') }}">@csrf<button class="btn">Install Missing Demo Data</button></form>@else<div class="flash">Demo showcase is fully populated.</div>@endif
@if($status['students']>0)<form method="post" action="{{ route('admin.demo-data.destroy') }}" onsubmit="return confirm('Remove all demo students, attempts, marksheets and sample certificates?')">@csrf @method('DELETE')<button class="btn btn-danger">Remove All Demo Data</button></form>@endif</section>
@endsection
