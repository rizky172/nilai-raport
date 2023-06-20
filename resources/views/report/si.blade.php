
@extends('report.layout')

@section('title', 'Sales Invoice')

@section('content')
<table class="gema-header valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left valign-top" width="50%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td><b>Kepada</b></td><td>:</td><td>{{ $customer['company_name'] }}</td></tr>
                        <!-- <tr><td class="valign-top"></td><td class="valign-top"></td><td>{{ $address }} - {{ $factory }} - {{ $customer['city'] }}, Indonesia</td></tr> -->
                        <tr><td class="valign-top"></td><td class="valign-top"></td><td>{{ $address }}</td></tr>
                        <tr><td><b>Nama</b></td><td>:</td><td>{{ $name }}</td></tr>
                        <td></td>
                    </tbody>
                </table>
            </td>
            <td class="text-left valign-top" width="50%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td><b>No. Faktur</b></td><td width="10px">:</td><td>{{ $ref_no }}</td></tr>
                        <!-- <tr><td><b>No. Surat Jalan</b></td><td width="10px">:</td><td>{{ $surat_jalan_ref_no }}</td></tr> -->
                        <tr><td><b>No. PO</b></td><td width="10px">:</td><td>{{ implode(', ', $no_so) }}</td></tr>
                        <!-- <tr><td><b>Tanggal Bayar</b></td><td width="10px">:</td><td>{{ $due_date }}</td></tr> -->
                        <tr><td><b>TOP</b></td><td width="10px">:</td><td>{{ $customer['billing_period'] }} Hari</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
<br>

<table class="gema valign-top" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th>No</th>
            <th>Keterangan</th>
            <th class="text-right">Qty</th>
            <th>Unit</th>
            <th class="text-right">Harga per Unit</th>
            <th class="text-right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail_surat_jalan as $key => $y)
            <tr class="no-border">
                <td>{{ $key + 1 }}</td>
                <td>{{ $y['name'] }}</td>
                <td class="text-right">{{ $y['qty'] }}</td>
                <td>{{ $y['unit'] }}</td>
                <td class="text-right">{{ NumberFormat::currency($y['price'], null) }}</td>
                <td class="text-right">{{ NumberFormat::currency($y['qty'] * $y['price'], null) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if ($ppn > 0)
        <tr class="no-border">
            <td colspan="4" rowspan="5"><b>#{{ ucfirst($terbilang) }}#</b></td>
            <td class="text-right"><strong>Sub-Total</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($sub_total, null) }}</strong></td>
        </tr>
        <tr class="no-border text-right">
            <td class="text-right"><strong>PPN 10%</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total_ppn, null) }}</strong></td>
        </tr>
        <tr class="no-border text-right">
            <td class="text-right"><strong>Grand Total</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total, null) }}</strong></td>
        </tr>
        @endif
        @if ($ppn == 0)
        <tr class="no-border">
            <td colspan="4"><b>#{{ ucfirst($terbilang) }}#</b></td>
            <td class="text-right"><strong>Grand Total</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total, null) }}</strong></td>
        </tr>
        @endif
    </tfoot>
</table>
<br>
<!-- <p><strong>Terbilang: {{ ucfirst($terbilang) }}</strong></p> -->
<p><strong>No. SJ :  {{ implode(', ', $no_sj) }}</strong></p>
<br>
@if(!empty($remark_pdf_sales_invoice))
<p><b>Notes</b></p>
<p>{!! nl2br($remark_pdf_sales_invoice) !!}</p>
<br>
@endif
<table style="margin-top: 40px; text-align: center;" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td rowspan="3" width="430px">&nbsp;</td>
            <td><strong>{{ $config_city }}, {{ $date }}</strong></td>
        </tr>
        <tr>
            @if($signature)
                <td class="text-center">
                    <img style="height: 70px;width:auto;" src="{{ $signature }}" alt="signature" />
                </td>
            @else
                <td height="100px"></td>
            @endif
        </tr>
        <tr>
            <td><strong>{{ $sales_invoice_signature }}</strong></td>
        </tr>
    </tbody>
</table>
@endsection
