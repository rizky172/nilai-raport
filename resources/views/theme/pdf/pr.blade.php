
@extends('report.layout')

@section('title', 'Purchase Request')

@section('content')
<?php
    $ref_no = 'ref-no';
    $created = '2020-12-10';

    $item = [
        'item' => 'Item name',
        'due_date' => $created,
        'qty' => 10,
        'unit' => 'kg'
    ];
    $detail = [];
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
?>
<table style="width: 100%">
    <tr>
        <td><strong>Nomor: {{ $ref_no }}</strong></td>
        <td align="right"><strong>Tanggal: {{ DateFormat::national(new \DateTime($created)) }}</strong></td>
    </tr>
</table>
<table class="gema" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th class="col-id">No</th>
            <th>Item</th>
            <th class="col-price">Tgl. Kebutuhan</th>
            <th class="col-price text-right">Qty</th>
            <th class="col-price">Unit</th>
        </tr>
    </thead>
    <tbody>
        {{ $no = 1 }}
        @foreach ($detail as $d)
            <tr class="no-border">
                <td>{{ $no }}</td>
                <td>{{ $d['item'] }}</td>
                <td>{{ $d['due_date'] }}</td>
                <td class="text-right">{{ $d['qty'] }}</td>
                <td>{{ $d['unit'] }}</td>
            </tr>
        {{ $no++ }}
        @endforeach
    </tbody>
</table>
@endsection
