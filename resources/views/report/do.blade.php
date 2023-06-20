@extends('report.layout')

@section('paper')
    @include('theme.partials.paper', [ 'paper' => 'dot-matrix' ])
@endsection

@section('header')
@endsection

@section('content')
<table class="valign-top" width="100%">
    <tr>
        <td width="50%">
<!-- First DO (START) -->
@include('report.partials.do', [ 'data' => $data1 ])
<!-- First DO (END)-->
        </td>
        <td>
<!-- Second DO (START) -->
@if(!empty($data2))
    @include('report.partials.do', [ 'data' => $data2 ])
@endif
<!-- Second DO (END)-->
        </td>
    </tr>
</table>
@endsection
