@extends('report.layout')

@section('title', 'Invoice (Pembayaran)')

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
            <th class="text-left">Tanggal</th>
            <th class="text-left">Keterangan</th>
            <th class="text-left">Metode</th>
            <th class="text-right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payment as $key => $y)
            <tr class="no-border">
                <td class="text-left">{{ $y['created'] }}</td>
                <td class="text-left">{{ $y['notes'] }}</td>
                <td class="text-left">{{ $y['category'] }}</td>
                <td class="text-right">{{ NumberFormat::currency($y['amount'], null) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="no-border text-right">
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>{{ NumberFormat::currency($total_paid, null) }}</strong></td>
        </tr>
        <!-- <tr class="no-border text-right">
            <td colspan="3"><strong>Terbayar</strong></td>
            <td><strong>{{ NumberFormat::currency($total_paid, null) }}</strong></td>
        </tr>
        <tr class="no-border text-right">
            <td colspan="3"><strong>Sisa</strong></td>
            <td><strong>{{ NumberFormat::currency($total - $total_paid, null) }}</strong></td> -->
        </tr>
    </tfoot>
</table>
<br>
<p><strong>Terbilang: {{ (isset($terbilang) && !empty($terbilang)) ? $terbilang : '' }}</strong></p>
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
