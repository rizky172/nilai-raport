@extends('theme.pdf.layout')

@section('title', 'Sales Quotation')

@section('header')
    @include('theme.partials.header', [ 'type' => 'dot-matrix' ])
@endsection

@section('content')
<table cellspacing="0" width="100%" border="1">
    <tbody>
        <tr>
            <td>Nomor</td>
            <td>: XXXX/XXXX/XXXX</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: -</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: Penawaran Harga Karton Box</td>
        </tr>
    </tbody>
</table>
<br>
<br>
<br>
<br>
Kepada Yth<br>
Bpk/Ibu (PIC)<br>
Company Name<br>
Ditempat<br>
<br>
Dengan hormat,<br>
<br>
Bersama ini kami sampaikan penawaran harga karton box sesuai
dengan permintaan Bapak. Rincian harga tersebut terlampir
sebagai berikut:
<br>
<table class="gema" width="100%" border="1" cellspacing="0">
    <thead>
        <tr>
            <th class="col-id">No</th>
            <th>Item</th>
            <th class="text-right col-qty">Min. Qty</th>
            <th class="text-right col-price">Harga</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1.</td>
            <td>
                Nama Item Yang Panjang<br>
                <b>Spec</b>: Art Paper 250<br>
                <b>Dimensi(p x l x t)</b>: 62 x 62 x 120 mm
            </td>
            <td class="text-right">50, 000</td>
            <td class="text-right">Rp. 100,203,000</td>
        </tr>
    </tbody>
</table>
<br>
<b>Syarat & Kondisi</b>
<br>
<table class="gema" width="100%" border="1" cellspacing="0">
    <tbody>
        <tr>
            <td>1.</td>
            <td>Pembayaran pertama (Down Payment/ DP)</td>
            <td>: 50% dari nilai tagihan.</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Pelunasan</td>
            <td>: 14 hari setelah tanggal invoice.</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Validitas harga</td>
            <td>: 14 hari setelah tanggal quotation.</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Harga</td>
            <td>: Belum termasuk pajak /PPn 10%</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Delivery</td>
            <td>: 14 hari setelah Purchase Order (PO)</td>
        </tr>
    </tbody>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
Hormat Kami,
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<b><u>Penanggung Jawab</u></b><br>
Jabatan

@endsection
