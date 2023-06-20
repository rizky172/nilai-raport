@extends('report.layout')

@section('title', 'Invoice')

@section('content')
<table class="gema-header valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left valign-top" width="50%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td><b>Kepada</b></td><td>:</td><td>{{ $person['name'] }}</td></tr>
                        <tr><td><b>Email</b></td><td>:</td><td>{{ $person['email'] }}</td></tr>
                        <tr></tr>
                        <td></td>
                    </tbody>
                </table>
            </td>
            <td class="text-left valign-top" width="50%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td><b>Tanggal Buat</b></td><td width="10px">:</td><td>{{ $created }}</td></tr>
                        <tr><td><b>Tanggal Bayar</b></td><td width="10px">:</td><td>{{ $due_date }}</td></tr>
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
            <th class="text-left">No</th>
            <th class="text-left">Produk</th>
            <th class="text-left">Qty</th>
            <th class="text-right">Harga</th>
            <th class="text-right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $key => $y)
            <tr class="no-border">
                <td class="text-left">{{ $key + 1 }}</td>
                <td class="text-left">
                    {{ $y['item_label'] }} <br>
                    {{ $y['notes'] }}
                </td>
                <td class="text-left">{{ $y['qty'] }}</td>
                <td class="text-right">{{ NumberFormat::currency($y['price'], null) }}</td>
                <td class="text-right">{{ NumberFormat::currency($y['qty'] * $y['price'], null) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="no-border text-right">
            <td colspan="4"><strong>Total</strong></td>
            <td><strong>{{ NumberFormat::currency($total, null) }}</strong></td>
        </tr>
        <tr class="no-border text-right">
            <td colspan="4"><strong>Terbayar</strong></td>
            <td><strong>{{ NumberFormat::currency($total_paid, null) }}</strong></td>
        </tr>
        <tr class="no-border text-right">
            <td colspan="4"><strong>Sisa</strong></td>
            <td><strong>{{ NumberFormat::currency($total - $total_paid, null) }}</strong></td>
        </tr>
    </tfoot>
</table>
<br>
<p><strong>Terbilang: {{ $terbilang ?? '' }}</strong></p>
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
            <td><strong>{{$city}}, {{ $date }}</strong></td>
        </tr>
        <tr>
            @if (!empty($signature))
                <td class="text-center">
                    <img style="height: 70px;width:auto;" src="{{ $signature }}" alt="signature" />
                </td>
            @else
                <td height="70px"></td>
            @endif
        </tr>
        <tr>
            <td><strong>{{$person_login}}</strong></td>
        </tr>
    </tbody>
</table>
@endsection
