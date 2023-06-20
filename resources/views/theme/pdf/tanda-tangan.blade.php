@extends('theme.pdf.layout')

@section('title', 'Test Title')

@section('content')
<?php
    $ref_no = 'xxx';
    $no_po = 'POPO';
    $created = '2020-10-10';

    $item = [
        'item' => '115 Carton Sheet (PxL) 1310x620mm K150/M125/K150 C/F 115 Carton Sheet (PxL) 1310x620mm K150/M125/K150 C/F 115 Carton Sheet (PxL) 1310x620mm K150/M125/K150 C/F',
        'qty' => 10000,
        'moved' => 100,
        'unit' => 'Kg'
    ];
    $detail = [];
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
    $detail[] = $item;
?>

<table cellspacing="0" style="width: 100%;" class="table-ttd-no-border">
    <tr>
        <td><strong>PPIC</strong></td>
        <td width="400px"> </td>
        <td><strong>Warehouse</strong></td>
    </tr>
    <tr>
        <td class="underline" height="100px"></td>
        <td></td>
        <td class="underline"></td>
    </tr>
</table>
@endsection
