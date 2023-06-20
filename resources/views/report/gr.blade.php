@extends('report.layout')

@section('title', 'Goods Received')

@section('content')
<table style="width: 100%" class="table-bold">
    <tr>
        <td width="10%">Nomor</td>
        <td>: {{ $ref_no }} XX</td>
        <td></td>
        <td width="13%"></td>
    </tr>
    <tr>
        <td>PO</td>
        <td>: {{ $no_po }}</td>
        <td align="right">Tanggal</td>
        <td align="right">: {{ DateFormat::national(new \DateTime($created)) }}</td>
    </tr>
</table>

<table class="gema valign-top" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id text-left">No</th>
            <th class="text-left">Item</th>
            <th class="col-qty text-right">Qty</th>
            <th class="col-qty text-right">Dipindahkan</th>
            <th class="col-unit text-center">Unit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detail as $key => $d)
            <tr class="no-border">
                <td>{{ $key + 1 }}</td>
                <td>{{ $d['item'] }}</td>
                <td class="text-right">{{ $d['qty'] }}</td>
                <td class="text-right">{{ $d['moved'] }}</td>
                <td class="text-center">{{ $d['unit'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
