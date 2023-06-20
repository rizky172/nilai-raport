@extends('report.layout')

@section('header')
    @include('report.partials.header', [
        'type' => 'salary-slip',
        'title' => 'Slip Gaji PT. GMA',
        'sub_title' => 'September'
    ])
@endsection

@section('content')
<?php
    $ref_no = 'ref_no';
    $created = '2020-10-10';
    $total = '150000';

    $person = [
        'company_name' => 'Company Name',
        'address' => 'Address',
        'name' => 'Name',
        'city' => 'City',
        'email' => 'email',
        'phone' => '02938-929383'
    ];

    $item = [
        'name' => 'Carton Box EE-115 360x275x400mm',
        'ref_no_customer' => null,
        'notes' => null,
        'unit' => 'kg',
        'qty' => '10',
        'price' => '15000'
    ];

    $detail = [];
    // One paper must hold max 7 item
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;

    $signature = URL::asset('/img/gema_logo_h64px.png');
?>
<table style="width: 100%">
    <tr>
        <td><strong>Nomor: {{ $ref_no }}</strong></td>
        <td align="right"><strong>Tanggal: {{ DateFormat::national(new \DateTime($created)) }}</strong></td>
    </tr>
</table>
<table class="gema-header valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left" width="100%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td colspan="3"><b>TO</b></td></tr>
                        <tr><td>Name</td><td>:</td><td>{{ $person['name'] }}</td></tr>
                        <tr><td>Address</td><td>:</td><td>{{ $person['address'] }}</td></tr>
                        <tr><td>Telephone</td><td>:</td><td>{{ $person['phone'] }}</td></tr>
                        <tr><td>Company</td><td>:</td><td>{{ $person['company_name'] }}</td></tr>
                        <tr><td>Email</td><td>:</td><td><a href="{{ $person['email'] }}">{{ $person['email'] }}</a></td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
<table class="gema valign-top" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th>Keterangan</th>
            <th class="text-right col-qty">Qty</th>
            <th class="text-right col-price">Harga</th>
            <th class="text-right col-price">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $d['name'] }}@if($d['notes']). {{ $d['notes'] }}@endif</td>
            <td class="text-right">{{ $d['qty'] }}</td>
            <td class="text-right">{{ NumberFormat::currency((int) $d['price'], null) }}</td>
            <td class="text-right">{{ NumberFormat::currency($d['qty'] * $d['price'], null) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-right"><strong>TOTAL<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total, null) }}</strong></td>
        </tr>
    </tfoot>
</table>

<br>
<br>

<div style="float: right; margin-right: 100px">
    <table style="margin-top: 40px; text-align: center;" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td height="200px"></td>
            </tr>
            <tr>
                <td><!--<u> $sales_marketing_manager </u>--></td>
            </tr>
            <tr>
                <td><strong>Manager</strong></td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
