@extends('report.layout')

@section('content')
<br>
<h3 class="text-center"><strong>Penawaran Harga</strong></h3>
<br>
<table class=" valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left valign-top" width="50%">
                <table width="100%" cellspacing="0">
                    <tbody>
                        <tr><td width="25%">Nomor Penawaran</td><td width="2%">:</td><td>{{ $ref_no}}</td></tr>
                        <tr><td width="25%">Tanggal Penawaran</td><td width="2%">:</td><td>{{ $created }}</td></tr>
                        <tr><td width="25%">Masa Berlaku Penawaran</td><td width="2%">:</td><td>{{ $due_date }} Hari</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
<br>
<table class=" valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left valign-top" width="50%">
                <table width="100%" cellspacing="0">
                    <tbody>
                        <tr><td width="50%"><b>Ditujukan Kepada</b></td><td width="2%">:</td><td></td></tr>
                        <tr><td width="50%">Nama Perusahaan</td><td width="2%">:</td><td>{{ $quotation_company_name }}</td></tr>
                        <tr><td width="50%">Nama PIC</td><td width="2%">:</td><td>{{ $quotation_company_pic }}</td></tr>
                    </tbody>
                </table>
            </td>
            <td class="text-left valign-top" width="50%">
                <table width="100%" cellspacing="0">
                    <tbody>
                        <tr><td width="50%"><b>Dibuat Oleh</b></td><td width="2%">:</td><td></td></tr>
                        <tr><td width="50%">Nama PIC Perusahaan</td><td width="2%">:</td><td>{{ $sales }}</td></tr>
                        <tr><td width="50%">Alamat Email</td><td width="2%">:</td><td>{{ (isset($company_email) ? $company_email : null ) }}</td></tr>
                        <tr><td width="50%">Nomor Telepon/Handphone</td><td width="2%">:</td><td>{{ (isset($company_phone) ? $company_phone : null ) }}</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
<br>
<table class="gema caraka-guna" width="100%">
    <thead>
        <tr class="text-center">
            <th>No</th>
            <th>Permintaan Barang</th>
            <th>Barang yang Ditawarkan</th>
            <th>Qty</th>
            <th>Sat</th>
            <th>Harga</th>
            <th>Total Harga</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $key => $x)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $x['request'] }}</td>
                <td>{{ $x['item'] }}</td>
                <td>{{ $x['qty'] }}</td>
                <td>{{ $x['unit'] }}</td>
                <td class="text-right">{{ number_format($x['price'],0,",",".") }}</td>
                <td class="text-right">{{ number_format($x['total'],0,",",".") }}</td>
                <td>{{ $x['is_stock'] ? 'Ready' : 'Tidak Tersedia' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<br>
<br>
<p>{!! nl2br($notes_footnote) !!}</p>
<p>Catatan :</p>
<form>
    <textarea>{{ $notes }}</textarea>
</form>
<br>
<table style="margin-left: 10px; margin-right: 10px; text-align: center; width: 100%;">
    <tbody>
        <tr>
            <td width="400px"></td>
            <td>Dibuat Oleh,</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="text-left"></td>
            <td><u><b>{{ $sales }}</b></u></td>
        </tr>
    </tbody>
</table>
@endsection
