@extends('report.layout')

@section('header')
    @include('report.partials.header', [
        'email' => $email_pic_production_plan
    ])
@endsection

@section('paper')
    @include('theme.partials.paper')
@endsection

@section('content')
<br>
<!-- <table style="width: 100%">
    <tr>
        <td align="left"><strong>No. Ref: {{ $ref_no }}</strong></td>
        <td align="right"><strong>Tanggal: {{ DateFormat::national(new \DateTime($created_at)) }}</strong></td>
        <td align="right"><strong>Status: {{ $status }}</strong></td>
    </tr>
</table>
<br> -->
<table>
    <tr>
        <td><strong>No. Ref: {{ $ref_no }}</strong></td>
    </tr>
    <tr>
        <td><strong>Tanggal: {{ DateFormat::national(new \DateTime($created_at)) }}</strong></td>
    </tr>
    <tr>
        <td><strong>Status: {{ $status }}</strong></td>
    </tr>
    <tr>
        <td>Tanggal Mulai: {{ DateFormat::national(new \DateTime($date_start)) }}</td>
    </tr>
    <tr>
        <td>Tanggal Selesai: {{ DateFormat::national(new \DateTime($date_finish)) }}</td>
    </tr>
</table>
<br>
<table class="gema valign-top" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th>Kode</th>
            <th>Item</th>
            <th>Unit</th>
            <th class="text-right">Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
            <tr class="no-border">
                <td>{{ $key + 1 }}</td>
                <td>{{ $d['item_ref_no'] }}</td>
                <td>{{ $d['item'] }}</td>
                <td>{{ $d['unit'] }}</td>
                <td class="text-right">{{ $d['qty'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"></td>
            <td><strong>TOTAL<strong></td>
            <td class="text-right"><strong>{{ $total_qty }}</strong></td>
        </tr>
    </tfoot>
</table>
<br>
<table cellspacing="0" style="width: 100%;" class="table-ttd-no-border valign-bottom">
    <!-- <tr>
        <td><strong>Sales & Marketing</strong></td>
        <td width="300px"> </td>
        <td><strong>Sales & Marketing Manager</strong></td>
    </tr>
    <tr>
        <td class="underline" height="150px"><strong></strong></td>
        <td></td>
        <td class="underline"><strong></strong></td>
    </tr> -->
</table>
@endsection
