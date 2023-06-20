@extends('report.layout')

@section('content')
<br>
<h3 class="text-center"><strong>Sales Order</strong></h3>
<br>
<br>
<table class="gema caraka-guna" width="100%">
    <thead>
        <tr class="text-center">
            <th>No</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit</th>
            <th class="text-right">Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detail as $key => $x)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $x['item'] }}</td>
                <td>{{ $x['qty'] }}</td>
                <td>{{ $x['unit'] }}</td>
                <td class="text-right">{{ number_format($x['price'],0,",",".") }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
