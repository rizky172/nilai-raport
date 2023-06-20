@extends('theme.pdf.layout')

@section('title', 'Purchase Order')

@section('content')
<style>
/*
p, table td {
    font-size: 12px;
    line-height: 1.2;
}
*/
</style>

<p class="title">No. PO: XXXXXXXXXX</p>
<p>Tgl. Buat: DD-MM-YY</p>
<p>Tgl. Dibutuhkan: DD-MM-YY</p>
<br>
<table class="gema-header" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <!-- <td class="text-left" width="50%">
                <p><b>To</b></p>
                <p>&nbsp;</p>
                <p><b>Vendor Name: </b> PT. BINTANG PADI SEJAHTERA</p>
                <p>Address: ITC KUNINGAN LT. 2 JEMBATAN 2, DKI Jakarta, Indonesia</p>
                <p>Telephone: 031-03998472</p>
                <p>Attention: Bapak Farid</p>
                <p>Email: <a href="mailto:farid@gmail.com">farid.102988@gmail.com</a></p>
            </td>
            <td class="text-left" width="50%">
                <p><b>Delivery Address</b></p>
                <p>&nbsp;</p>
                <p><b>Receiver Name: </b> PT. Gema Putra Abadi</p>
                <p>Address: Jl. Rawa Bogo No.123, Kel. Paduren, Kec. Mustika</p>
                <p>Kode Pos: 601928</p>
                <p>Kota: Bekasi</p>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
            </td> -->
            <td class="text-left" width="50%" valign="top">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td colspan="2" height="30px" valign="top"><b>To</b></td></tr>
                        <tr><td><b>Vendor Name</b></td><td>: PT. BINTANG PADI SEJAHTERA</td></tr>
                        <tr><td>Address</td><td>: ITC KUNINGAN LT. 2 JEMBATAN 2, DKI Jakarta, Indonesia</td></tr>
                        <tr><td>Telephone</td><td>: 031-03998472</td></tr>
                        <tr><td>Company</td><td>: PT. BINTANG PADI SEJAHTERA</td></tr>
                        <tr><td>Attention</td><td>: Bapak Farid</td></tr>
                        <tr><td>Email</td><td>: <a href="mailto:farid@gmail.com">farid.102988@gmail.com</a></td></tr>
                    </tbody>
                </table>
            </td>
            <td class="text-left" width="50%" valign="top">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td colspan="2" height="30px" valign="top"><b>Delivery Address</b></td></tr>
                        <tr><td><b>Receiver Name</b></td><td>: PT. Gema Putra Abadi</td></tr>
                        <tr><td>Address</td><td>: Jl. Rawa Bogo No.123, Kel. Paduren, Kec. Mustika</td></tr>
                        <tr><td>Kode Pos</td><td>: 601928</td></tr>
                        <tr><td>Kota</td><td>: Bekasi</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
<p><b>Delivery Notes</b></p>
<p>PO Susulan Kiriman Tanggal 26/02/2019 Sampai Tanggal</p>
<br>
<table class="gema" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th>Description</th>
            <th class="text-right col-qty">Qty</th>
            <th class="col-unit">Unit</th>
            <th class="text-right col-price">Harga Per Unit</th>
            <th class="text-right col-price">Total</th>
        </tr>
    </thead>
    <tbody>
        <tr class="no-border">
            <td>1.</td>
            <td>WOODEN C60</td>
            <td class="text-right">125</td>
            <td>Kilogram</td>
            <td class="text-right">132,500</td>
            <td class="text-right">100,203,000</td>
        </tr>
        <tr class="no-border">
            <td>2.</td>
            <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit</td>
            <td class="text-right">125</td>
            <td>Kilogram</td>
            <td class="text-right">132,500</td>
            <td class="text-right">100,203,000</td>
        </tr>
        <tr class="no-border">
            <td>3.</td>
            <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec bibendum posuere lectus, quis viverra libero feugiat ut.</td>
            <td class="text-right">125</td>
            <td>Kilogram</td>
            <td class="text-right">132,500</td>
            <td class="text-right">100,203,000</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right"><strong>Sub-Total<strong></td>
            <td class="text-right"><strong>10.000.000.000</strong></td>
        </tr>
        <tr>
            <td colspan="5" class="text-right"><strong>PPN +10%<strong></td>
            <td class="text-right"><strong>10.000.000.000</strong></td>
        </tr>
        <tr>
            <td colspan="5" class="text-right"><strong>PPH +5%<strong></td>
            <td class="text-right"><strong>10.000.000.000</strong></td>
        </tr>
        <tr>
            <td colspan="5" class="text-right"><strong>TOTAL<strong></td>
            <td class="text-right"><strong>10.000.000.000</strong></td>
        </tr>
    </tfoot>
</table>
<br>
<p>Notes</b></p>
<p>- One</b></p>
<p>- Two</b></p>

@endsection
