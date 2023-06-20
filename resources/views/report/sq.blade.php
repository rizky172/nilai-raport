@extends('report.layout')

@section('title', 'Sales Quotation')

@section('content')
<table cellspacing="0" width="100%" border="1">
    <tbody>
        <tr>
            <td style="width: 70px"><strong>Nomor</strong></td>
            <td>{{ $ref_no }}</td>
        </tr>
        <tr>
            <td><strong>Lampiran</strong></td>
            <td>-</td>
        </tr>
        <tr>
            <td><strong>Perihal</strong></td>
            <td>Penawaran Harga {{ $item_category_label }}</td>
        </tr>
    </tbody>
</table>
<br>
Kepada Yth<br>
Bpk/Ibu <strong>{{ $person->name }}</strong><br>
<strong>{{ $person->company_name }}</strong><br>
Ditempat<br>
<br>
Dengan hormat,<br>
Bersama ini kami sampaikan penawaran harga {{ strtolower($item_category_label) }} sesuai
dengan permintaan Bapak. Rincian harga tersebut terlampir
sebagai berikut:
<br>
<table class="gema valign-top" width="100%" border="1" cellspacing="0">
    <thead>
        <tr>
            <th class="col-id">No</th>
            <th>Item</th>
            <th class="text-right col-qty">Min. Qty</th>
            <th class="text-right col-price">Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>
                {{ $d['item'] }}<br>
                <b>Spec</b>: <br>
                <b>Dimensi(p x l x t)</b>: {{ $d['material_length'] }} x {{ $d['material_width'] }} x {{ $d['material_height'] }} cm
            </td>
            <td class="text-right">{{ $d['qty'] }}</td>
            <td class="text-right">Rp.{{ $d['price'] }}/{{ $d['unit'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
<b>Syarat & Kondisi</b>
<br>
<table class="gema" width="100%" border="1" cellspacing="0">
    <tbody>
        <tr>
            <td class="col-id">1.</td>
            <td>Pembayaran pertama (Down Payment/ DP)</td>
            <td>{{ $down_payment }}% dari nilai tagihan.</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Pelunasan</td>
            <td>{{ $repayment }} hari setelah tanggal invoice.</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Validitas harga</td>
            <td>{{ $valid_price }} hari setelah tanggal quotation.</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Harga</td>
            <td><b>{{ $ppn == '1' ? 'Termasuk pajak /PPn 10%' : 'Belum termasuk pajak /PPn 10%'  }}</b></td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Delivery</td>
            <td>{{ $delivery }} hari setelah Purchase Order (PO)</td>
        </tr>
    </tbody>
</table>
<br>
Hormat Kami,
<br>
<br>
<br>
<br>
<br>
<br>
<strong><u>{{ $sales_marketing_manager }}</u></strong><br>
<strong>Sales Marketing Manager</strong>
<!-- { $user->position }} -->
@endsection
