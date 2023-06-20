@extends('report.layout')

@section('header')
    @include('report.partials.header', [
        'type' => 'salary-slip',
        'title' => $company_name,
        'sub_title' => 'Sales Order'
    ])
@endsection

@section('content')
<table style="width: 100%">
    <tr>
        <td align="left"><strong>No. SO: {{ $ref_no }}</strong></td>
        <td align="right"><strong>Tanggal: {{ DateFormat::national(new \DateTime($created)) }}</strong></td>
    </tr>
</table>
<table class="gema-header valign-top" cellspacing="0" width="100%">
z    <tbody>
        <tr>
            <td class="text-left" width="100%" class="valign-top">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr>
                            <td width="100px"><b>Name</b></td>
                            <td width="10px">:</td>
                            <td>{{ $person['company_name'] }}</td>
                        </tr>
                        <tr>
                            <td><b>Alamat</b></td>
                            <td>:</td>
                            <td>{{ $address }}, {{ $city }}, Indonesia</td>
                        </tr>
                        <tr>
                            <td><b>No. PO</b></td>
                            <td>:</td>
                            <td>{{ $ref_no_customer }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
@if(!empty($notes))
<p><b>Delivery Notes</b></p>
<p>{{ $notes }}</p>
<br>
@endif
<table class="gema valign-top" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th width="20%">Kode</th>
            <th width="30%">Item</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
            <tr class="no-border">
                <td>{{ $key + 1 }}</td>
                <td>{{ $d['ref_no'] }}</td>
                <td>{{ $d['name'] }}</td>
                <td class="text-right">{{ $d['qty'] }}</td>
                <td class="text-right">{{ NumberFormat::currency((int) $d['price'], null) . '/' . $d['unit'] }}</td>
                <td class="text-right">{{ NumberFormat::currency($d['qty'] * $d['price'], null) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if ($ppn != '0.00')
        <tr>
            <td colspan="5" class="text-right"><strong>Sub-Total<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total['sub_total'], null) }}</strong></td>
        </tr>
        <tr>
            <td colspan="5" class="text-right"><strong>PPN +{{ $ppn }}%<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total['ppn'], null) }}</strong></td>
        </tr>
        @endif
        <tr>
            <td colspan="5" class="text-right"><strong>TOTAL<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total['grand_total'], null) }}</strong></td>
        </tr>
        <tr class="no-border">
            <td colspan="5" class="text-right"><strong>DP {{ $down_payment }}%</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($down_payment_price, null) }}</strong></td>
        </tr>
        <tr>
            <td colspan="5" class="text-right"><strong>Grand Total<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total['grand_total'] - $down_payment_price, null) }}</strong></td>
        </tr>
    </tfoot>
</table>
<br>
<!--
<p><b>Notes</b></p>
<p>{!! nl2br($remark_pdf_po) !!}</p>
<br>
<br>
-->
<table cellspacing="0" style="width: 100%;" class="table-ttd-no-border valign-bottom">
    <tr>
        <td><strong>Sales & Marketing</strong></td>
        <td width="300px"> </td>
        <td><strong>Sales & Marketing Manager</strong></td>
    </tr>
    <tr>
        <td class="underline" height="150px"><strong>{{ $sales }}</strong></td>
        <td></td>
        <td class="underline"><strong>{{ $sales_marketing_manager }}</strong></td>
    </tr>
</table>
@endsection
