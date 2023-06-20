
@extends('report.layout')

@section('header')
    @include('report.partials.header', [
        'type' => 'surat-jalan',
        'title' => 'Surat Jalan',
        'sub_title' => $created_at,
        'email' => $email_pic_sales
    ])
@endsection

@section('paper')
    @include('theme.partials.paper')
@endsection

@section('content')
<table class="gema-header valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left" width="50%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <!-- <tr><td colspan="3" height="30px"><b>Kepada :</b></td></tr> -->
                        <tr><td><b>Kepada</b></td><td>:</td><td>{{ $person['company_name'] }}</td></tr>
                        <tr><td></td><td></td><td>{{ $address }}, {{ $person['city'] }}, Indonesia</td></tr>
                        <!-- <tr><td>Telephone</td><td>:</td><td>{ $phone }}</td></tr> -->
                        <tr><td><b>UP</b></td><td>:</td><td>{{ $person_name }}</td></tr>
                        <!-- <tr><td>Email</td><td>:</td><td><a href="mailto:{ $person['email'] }}">{ $person['email'] }}</a></td></tr> -->
                    </tbody>
                </table>
            </td>
            <td class="text-left" width="50%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <!-- <tr><td colspan="3" height="30px"><b>Delivery Address :</b></td></tr> -->
                        <tr><td width="80px"><b>No. SJ</b></td><td width="20px">:</td><td>{{ $ref_no }}</td></tr>
                        <tr><td><b>No. Ref</b></td><td>:</td><td>{{ $no_po }}</td></tr>
                        <tr><td><b>Notes</b></td><td>:</td><td>{!! nl2br($notes) !!}</td></tr>
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
            <th>Nama Barang</th>
            <th class="text-right col-qty">Jumlah</th>
            <th class="text-right col-unit">Satuan</th>
            <th class="text-left" style="width: 120px">Ket.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
            <tr class="no-border">
                <td>{{ $key + 1 }}</td>
                <td>
                    {{ $d['name'] }}@if($d['notes']). {{ $d['notes'] }}@endif
                </td>
                <td class="text-right">{{ $d['qty'] }}</td>
                <td class="text-right">{{ $d['unit'] }}</td>
                <td class="text-left">{{ $d['notes'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<!-- <p><b>Detail Surat Jalan yang dicetak hanya yang berstatus Dikirim dan Retur</b></p> -->
<table cellspacing="0" style="width: 100%;" class="table-ttd">
    <tr>
        <td>Warehouse</td>
        <td>QC</td>
        <td>Marketing</td>
        <td><strong>Customer/Penerima</strong></td>
    </tr>
    <tr>
        <td height="60px"></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
@if(!empty($remark_pdf_surat_jalan))
    <br>
    <p class="text-center" style="color: red"><b>{!! nl2br($remark_pdf_surat_jalan) !!}</b></p>
    <br>
@endif
<p class="text-center"><b>(Putih - Accounting) ; (Merah - PPIC) ; (Kuning - Marketing) ; (Hijau - Produksi) ; (Biru - Customer)</b></p>
@endsection
