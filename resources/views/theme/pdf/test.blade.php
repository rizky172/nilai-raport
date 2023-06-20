@extends('theme.pdf.layout-do')

@section('content')
<style>

p, table td {
    font-size: 12px !important;
}

table {
    border-collapse: collapse;
}

table, th, td {
  border: 1px solid black;
}

th, td {
  padding: 2px;
}
</style>

<div>
    <p>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz</p>
    <p>ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz</p>
    <table style="width:100%;">
        <thead>
            <tr>
                <th class="text-left">Column 1</th>
                <th class="text-left">Column 2</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Row 1</td>
                <td>Row 2</td>
            </tr>
            <tr>
                <td>Row 1</td>
                <td>Row 2</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
