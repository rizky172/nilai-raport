@extends('report.layout')

@section('title', 'Purchase Order')

@section('content')
<table style="width: 100%" border="0">
    <tr>
        <td><strong>Nomor: {{ $ref_no }}</strong></td>
        <td align="right"><strong>Tanggal: {{ DateFormat::national(new \DateTime($created)) }}</strong></td>
    </tr>
</table>
<table class="gema-header valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left" width="50%">
                <!-- Address -->
                <table width="100%" class="address valign-top" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="3"><b>TO:</b></td>
                        </tr>
                        <tr>
                            <td><b>Vendor</b></td>
                            <td>:</td>
                            <td>{{ $person['company_name'] }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>:</td>
                            <td>{{ $address }}, {{ $city }}, Indonesia</td>
                        </tr>
                        <tr>
                            <td>Telephone</td>
                            <td>:</td>
                            <td>{{ $phone }}</td>
                        </tr>
                        <tr>
                            <td>Company</td>
                            <td>:</td>
                            <td>{{ $person['company_name'] }}</td>
                        </tr>
                        <tr>
                            <td>Attention</td>
                            <td>:</td>
                            <td>{{ $person['name'] }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><a href="mailto:{{ $person['email'] }}">{{ $person['email'] }}</a></td>
                        </tr>
                    </tbody>
                </table>
                <!-- /Table Address -->
            </td>
            <td class="text-left" width="50%">
                <!-- Table Address -->
                <table width="100%" class="address valign-top" cellspacing="0">
                   <tbody>
                      <tr>
                         <td colspan="3"><b>Delivery Address:</b></td>
                      </tr>
                      <tr>
                         <td width="80px"><b>Receiver Name</b></td>
                         <td width="10px">:</td>
                         <td>PT. Gema Putra Abadi</td>
                      </tr>
                      <tr>
                         <td>Address</td>
                         <td>:</td>
                         <td>Jl. Rawa Bogo No.123, Kel. Paduren, Kec. Mustika</td>
                      </tr>
                      <tr>
                         <td>Kode Pos</td>
                         <td>:</td>
                         <td>601928</td>
                      </tr>
                      <tr>
                         <td>Kota</td>
                         <td>:</td>
                         <td>Bekasi</td>
                      </tr>
                   </tbody>
                </table>
                <!-- /Table Address -->
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
<table class="gema valign-top" style="width: 100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th>Description</th>
            <th class="text-right col-date">Tgl. Dikirim</th>
            <th class="text-right col-qty">Qty</th>
            <th class="text-left col-unit">Unit</th>
            <th class="text-right col-price-per-unit">Harga Per Unit</th>
            <th class="text-right col-price">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
            <tr class="no-border">
                <td>{{ $key + 1 }}</td>
                <td>{{ $d['name'] }}</td>
                <td class="text-right">{{ DateFormat::national(new \DateTime($d['due_date'])) }}</td>
                <td class="text-right">{{ $d['qty'] }}</td>
                <td>{{ $d['unit'] }}</td>
                <td class="text-right">{{ NumberFormat::currency((int) $d['price'], null) }}</td>
                <td class="text-right">{{ NumberFormat::currency($d['qty'] * $d['price'], null) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if ($ppn > 0 || $pph > 0)
        <tr>
            <td colspan="6" class="text-right"><strong>Sub-Total<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total['sub_total'], null) }}</strong></td>
        </tr>
            @if($ppn > 0)
            <tr>
                <td colspan="6" class="text-right"><strong>PPN +{{ $ppn }}%<strong></td>
                <td class="text-right"><strong>{{ NumberFormat::currency($total['ppn'], null) }}</strong></td>
            </tr>
            @endif
            @if($pph > 0)
            <tr>
                <td colspan="6" class="text-right"><strong>PPH +{{ $pph }}%<strong></td>
                <td class="text-right"><strong>{{ NumberFormat::currency($total['pph'], null) }}</strong></td>
            </tr>
            @endif
        @endif
        <tr>
            <td colspan="6" class="text-right"><strong>TOTAL<strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($total['grand_total'], null) }}</strong></td>
        </tr>
    </tfoot>
</table>
<br>
<p><b>Notes</b></p>
<p>{!! nl2br($remark_pdf_po) !!}</p>

@endsection
