@extends('theme.pdf.layout')

@section('title', 'Surat Jalan')

@section('paper')
    @include('theme.partials.paper', [ 'paper' => 'dot-matrix' ])
@endsection

@section('header')
    @include('theme.partials.header', [ 'type' => 'dot-matrix' ])
@endsection

@section('content')
<?php
    $ref_no = 'ref_no';

    $person = [
        'company_name' => 'Company Name',
        'address' => 'Address',
        'name' => 'Name',
        'city' => 'City',
        'email' => 'email'
    ];

    $item = [
        'name' => 'Carton Box EE-115 360x275x400mm',
        'ref_no_customer' => null,
        'notes' => null,
        'unit' => 'kg',
        'qty' => '10'
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
<table class="gema-header" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left" width="50%" valign="top">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <!-- <tr><td colspan="3" height="30px" valign="top"><b>Kepada :</b></td></tr> -->
                        <tr><td><b>Kepada</b></td><td>:</td><td>{{ $person['company_name'] }}</td></tr>
                        <tr><td valign="top"></td><td valign="top"></td><td>{{ $person['address'] }}, {{ $person['city'] }}, Indonesia</td></tr>
                        <!-- <tr><td>Telephone</td><td>:</td><td>{ $phone }}</td></tr> -->
                        <tr><td><b>UP</b></td><td>:</td><td>{{ $person['name'] }}</td></tr>
                        <!-- <tr><td>Email</td><td>:</td><td><a href="mailto:{ $person['email'] }}">{ $person['email'] }}</a></td></tr> -->
                    </tbody>
                </table>
            </td>
            <td class="text-left" width="50%" valign="top">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <!-- <tr><td colspan="3" height="30px" valign="top"><b>Delivery Address :</b></td></tr> -->
                        <tr><td><b>No. Surat Jalan</b></td><td width="10px">:</td><td>{{ $ref_no }}</td></tr>
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
            <th class="col-id text-left">No</th>
            <th class="text-left">Nama Barang</th>
            <th class="col-qty text-right">Jumlah</th>
            <th class="col-unit text-right">Satuan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $key => $value)
            <tr class="no-border" align="top">
                <td valign="top">{{ $key + 1 }}</td>
                <td valign="top">
                    {{ $value['name'] }}
                    @if($value['ref_no_customer'])
                    <br>
                    {{ $value['ref_no_customer'] }}
                    @endif
                    @if($value['notes'])
                    <br>
                    {{ $value['notes'] }}
                    @endif
                </td>
                <td class="text-right">{{ $value['qty'] }}</td>
                <td class="text-right">{{ $value['unit'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<table cellspacing="0" style="width: 100%;" class="table-ttd">
    <tr>
        <td>PPIC</td>
        <td>Warehouse</td>
        <td>QC</td>
        <td>Marketing</td>
        <td>Pengirim</td>
        <td>Security</td>
        <td><strong>Customer/Penerima</strong></td>
    </tr>
    <tr>
        <td height="60px"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
        </td>
    </tr>
</table>
<p class="text-center"><b>(Putih - Accounting) ; (Merah - PPIC) ; (Kuning - Marketing) ; (Hijau - Produksi) ; (Biru - Customer)</b></p>
@endsection
