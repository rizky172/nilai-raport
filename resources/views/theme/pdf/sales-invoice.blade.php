@extends('theme.pdf.layout')

@section('title', 'Faktur')

@section('content')
<style>
.row table {
    float:left;
}
</style>

<div class="row">
    <table cellspacing="0" width="50%" border="0">
        <tbody>
            <tr>
                <td>Kepada Yth,</td>
            </tr>
            <tr>
                <td>PT. Toyota Motor Manufacturing Indonesia</td>
            </tr>
            <tr>
                <td>Finance Division</td>
            </tr>
            <tr>
                <td>Sunter II</td>
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" width="50%" border="0">
        <tbody>
            <tr>
                <td>Nomor Faktur</td>
                <td>: XXXX/XXXX/XXXX</td>
            </tr>
            <tr>
                <td>Nomor Surat Jalan</td>
                <td>: XXXX/XXXX/XXXX</td>
            </tr>
            <tr>
                <td>Nomor PO</td>
                <td>: XXXX/XXXX/XXXX</td>
            </tr>
            <tr>
                <td>Waktu Pembayaran</td>
                <td>: XX Hari</td>
            </tr>
        </tbody>
    </table>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<table cellspacing="0" width="100%" border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Keterangan</th>
            <th>Qty</th>
            <th>Harga Satuan</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>X</td>
            <td>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</td>
            <td>XX</td>
            <td>X.XXX</td>
            <td>XX.XXX.XXX</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" rowspan="3">
                Terbilang: XXXXXXXXX XXXXXXXXXXXXXXXXXXXXXXX XXXXXXXXXXXXXXXXXXXXX XXXXXXXXXXXXXXXXXXXXX
            </td>
            <td>Total</td>
            <td>XXX.XXX.XXX</td>
        </tr>
        <tr>
            <td>PPN 10%</td>
            <td>XX.XXX.XXX</td>
        </tr>
        <tr>
            <td>Total Tagihan</td>
            <td>XXX.XXX.XXX</td>
        </tr>
    </tfoot>
</table>

<br>

<div class="row">
    <table cellspacing="0" width="50%" border="0">
        <tbody>
            <tr>
                <td>MOHON DI TRANSFER KE REKENING :</td>
            </tr>
            <tr>
                <td>AN. PT. GEMA PUTRA ABADI</td>
            </tr>
            <tr>
                <td>BANK BNI SYARIAH CAB. SYARIAH BEKASI</td>
            </tr>
            <tr>
                <td>No. REK. 666.222.3454</td>
            </tr>
        </tbody>
    </table>
    <table cellspacing="0" width="50%" border="0">
        <tbody class="text-center">
            <tr>
                <td>Bekasi, 07 April 2020</td>
            </tr>
            <tr>
                <td>
                    <br><br><br><br>
                </td>
            </tr>
            <tr>
                <td>( LUCKY CAHYO KENCONO ) <br> DIREKTUR</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
