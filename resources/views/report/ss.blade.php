@extends('report.layout')

@section('header')
    @include('report.partials.header', [
        'type' => 'salary-slip',
        'title' => 'Slip ' . $title . ' PT. Gema Putra Abadi',
        'sub_title' => $month
    ])
@endsection

@section('content')
<table style="width: 100%">
    <tr>
        <td><strong>Nomor: {{ $ref_no }}</strong></td>
        <td align="right"><strong>Tanggal: {{ $created_at }}</strong></td>
    </tr>
</table>
<table class="gema-header valign-top" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="text-left" width="100%">
                <table width="100%" class="address" cellspacing="0">
                    <tbody>
                        <tr><td>Name</td><td>:</td><td>{{ $person['name'] }}</td></tr>
                        <tr><td>NIP</td><td>:</td><td>{{ $person['ref_no'] }}</td></tr>
                        <tr><td>Department</td><td>:</td><td>{{ $person['department']['label'] }}</td></tr>
                        <tr><td>Job Title</td><td>:</td><td>{{ $person['job_title']['label'] }}</td></tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br>
<table class="gema valign-top" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th>Keterangan</th>
            <th></th>
            <th class="text-right col-price">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $d['name'] }}</td>
            <td></td>
            <td class="text-right">{{ NumberFormat::currency($d['qty'] * $d['price'], 'Rp.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-left">@if($salary_slip_category == 'gaji' && $sisaPinjaman > 0) SISA PINJAMAN {{ NumberFormat::currency($sisaPinjaman, 'Rp.') }} @endif</td>
            <td colspan="1" class="text-left"><strong>TOTAL PENERIMAAN</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($income, 'Rp.') }}</strong></td>
        </tr>
        <tr>
            <td colspan="2" class="text-left"><!-- SISA CUTI: {{ $rest_paid_leave }} --></td>
            <td colspan="1" class="text-left"><strong>TOTAL POTONGAN</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency(abs($expense), 'Rp.') }}</strong></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td colspan="1" class="text-left"><strong>GRAND TOTAL/TAKE HOME PAY(THP)</strong></td>
            <td class="text-right"><strong>{{ NumberFormat::currency($income - abs($expense), 'Rp.') }}</strong></td>
        </tr>
    </tfoot>
</table>

<br>
<br>

<div style="float: right; margin-right: 100px">
    <table style="margin-top: 40px; text-align: center;" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td rowspan="3" width="430px">&nbsp;</td>
                <td><strong>{{ $city }}, {{ $date }}</strong></td>
            </tr>
            <tr>
                @if($signature)
                    <td class="text-center">
                        <img style="height: 70px;width:auto;" src="{{ $signature }}" alt="signature" />
                    </td>
                @else
                    <td height="70px"></td>
                @endif
            </tr>
            <tr>
                <td><strong>{{ $salary_slip_signature }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
