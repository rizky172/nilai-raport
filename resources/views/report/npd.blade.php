@extends('report.layout')

@section('header')
    @include('report.partials.header', [
        'email' => $email_pic_sales
    ])
@endsection

@section('title', 'New Product Development')

@section('content')
<style>
/*
p, table td {
    font-size: 12px;
    line-height: 1.2;
}
*/
p {
    margin-bottom: 5px;
}

.border-bottom {
    border-bottom: 1px solid black;
}

.table-spacing {
    border-spacing: 0;
}

.col-label {
    width: 10%;
}

.border-bottom-0 {
    border-bottom: 0;
}

.vis-hide {
    visibility: hidden;
}

</style>

<table cellspacing="0" cellpadding="0" style="width: 100%">
    <tr>
        <td width="15%"><p class="title">Nomor NPD: </p></td>
        <td width="60%"><p class="title">{{ $ref_no }}</p></td>
    </tr>
    <tr>
        <td width="15%"><p class="title">Nama NPD: </p></td>
        <td width="60%"><p class="title">{{ $name }}</p></td>
    </tr>
    <tr>
        <td width="15%"><p class="title">Status: </p></td>
        <td width="60%"><p class="title">{{ $status->label }}</p></td>
    </tr>
</table>
<br>
<p><b>Biodata</b></p>
<table cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td class="col-price">Perusahaan</td>
            <td>: {{ $company_name }}</td>
        </tr>
        <tr>
            <td>PIC</td>
            <td>: {{ $pic }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $address }}</td>
        </tr>
        <tr>
            <td>Kota</td>
            <td>: {{ $city }}</td>
        </tr>
        <tr>
            <td>No. Tlp</td>
            <td>: {{ $phone }}</td>
        </tr>
        <tr class="mb-10">
            <td>Email</td>
            <td>: {{ $email }}</td>
        </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr>
            <td>Sales</td>
            <td>: {{ $sales_name }}</td>
        </tr>
    </tbody>
</table>
<hr>
<p>Catatan:</p>
<p>{{ nl2br($notes) }}</p>
<br>
<br>
<p><b>Permintaan Barang</b></p>
<table cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="8%">Jenis Product</td>
            <td>: {{ $jenis_product_label }}</td>
        </tr>

        @if($jenis_product == 'carton-box')
        <tr>
            <td class="col-price">Jenis Box</td>
            <td>: {{ $jenis_box }}</td>
        </tr>
        <tr>
            <td class="col-price">Substansi</td>
            <td>: {{ $substansi }}</td>
        </tr>
        <tr>
            <td class="col-price">Wall</td>
            <td>: {{ $is_double }}</td>
        </tr>
        <tr>
            <td class="col-price">Flute</td>
            <td>: {{ $flute }}</td>
        </tr>
        @endif

        <tr>
            <td class="col-id">Dimensi(p x l x t)</td>
            <td>: {{ $dimension_length }} x {{ $dimension_width }} @if($jenis_product != 'printing') x {{ $dimension_height }} @endif</td>
        </tr>

        @if($jenis_product == 'foam')
        <tr>
            <td class="col-price">Warna</td>
            <td>: {{ $warna }}</td>
        </tr>
        <tr>
            <td class="col-price">Hardness</td>
            <td>: {{ $hardness }}</td>
        </tr>
        <tr>
        @endif

        @if($jenis_product == 'printing')
        <tr>
            <td class="col-price">Jenis Printing</td>
            <td>: {{ $jenis_printing }}</td>
        </tr>
        <tr>
            <td class="col-price">Warna Background</td>
            <td>: {{ $warna_background }}</td>
        </tr>
        <tr>
            <td class="col-price">Warna Font</td>
            <td>: {{ $warna_font }}</td>
        </tr>
        <tr>
            <td class="col-price">Jenis Font</td>
            <td>: {{ $jenis_font }}</td>
        </tr>
        <tr>
            <td class="col-price">Bahan</td>
            <td>: {{ $bahan }}</td>
        </tr>
        @endif
    </tbody>
</table>
<br>
<br>
<p><b>Hasil</b></p>
@if($final)
<table cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="20%">Jenis Produksi</td>
            <td>: {{ ($final["is_maklon"] == 1 ? 'Maklon' : 'Inhouse')  }}</td>
        </tr>
        
        <tr>
            <td>Ukuran Material(<i>p x l<?php if($jenis_product == 'foam'): ?> x t <?php endif; ?></i>)</td>
            <td>: <i>{{ $final["material_length"] }} x {{ $final["material_width"] }}  
            @if($jenis_product == 'foam')<span> x {{ $final["material_height"] }} </span> @endif </i></td>
        </tr>
        <tr>
            <td>Jenis Proses</td>
            <td>: {{ implode(", ", collect($final['process_category'])->pluck('label')->all()) }}</td>
        </tr>
        <tr>
            <td>Proses Tambahan</td>
            <td>: {{ implode(", ", collect($final['additional_process'])->pluck('label')->all()) }}</td>
        </tr>
        <tr>
            <td>Material Tambahan</td>
            <td>: {{ implode(", ", collect($final['additional_material'])->pluck('label')->all()) }}</td>
        </tr>
        @if($jenis_product == 'carton-box')
        <tr>
            <td>MOQ</td>
            <td>: {{ $final["moq"] }}</td>
        </tr>
        <tr>
            <td>TOP(Term Of Payment)</td>
            <td>: {{ $final["term_of_payment"] }}</td>
        </tr>
        <tr>
            <td>Jenis Project</td>
            <td>: {{ ($final["is_repeat_order"] == 1 ? 'Repeat Order' : 'Project') }}</td>
        </tr>
        @endif
    </tbody>
</table>
<br>
<br>
<p><b>Rincian Harga</b></p>

<table class="gema table-spacing" width="100%">
    <thead class="border-top border-bottom">
        <tr>
            <th width="80%">Deskripsi</th>
            <th class="text-right"></th>
            <th class="text-right">Harga</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $totalPrice = floatval($final["material_price"]) + floatval($final["diecut_price"]) + floatval($final["process_price"]) + floatval($final["transport_price"]);
            $totalMargin = $totalPrice * $final["margin"] / 100;
            $sellingPrice = $totalPrice + $totalMargin;
        ?>
        <tr>
            <td>Harga Material</td>
            <td class="text-right">Rp.</td>
            <td class="text-right col-price">{{ number_format($final["material_price"],0,",",".") }}</td>
        </tr>
        <tr v-if="data.product_category == 'carton-box'">
            <td>Harga Diecut</td>
            <td class="text-right">Rp.</td>
            <td class="text-right col-price">{{ number_format($final["diecut_price"],0,",",".") }}</td>
        </tr>
        <tr>
            <td>Harga Proses</td>
            <td class="text-right">Rp.</td>
            <td class="text-right col-price">{{ number_format($final["process_price"],0,",",".") }}</td>
        </tr>
        <tr>
            <td>Transport</td>
            <td class="text-right">Rp.</td>
            <td class="text-right col-price">{{ number_format($final["transport_price"],0,",",".") }}</td>
        </tr>
        <tr>
            <td class="text-right border-bottom"><b>Total Harga(HPP)</b></td>
            <td class="text-right border-bottom"><b>Rp.</b></td>
            <td class="text-right col-price border-bottom"><b>{{ number_format($totalPrice,0,",",".") }}</b></td>
        </tr>
        <tr>
            <td class="text-right border-bottom">Margin ( {{ $final["margin"] }}%)</td>
            <td class="text-right border-bottom">Rp.</td>
            <td class="text-right col-price border-bottom">{{ number_format($totalMargin,0,",",".") }}</td>
        </tr>
        <tr>
            <td class="text-right border-bottom"><b>Harga Jual</b></td>
            <td class="text-right border-bottom"><b>Rp.</b></td>
            <td class="text-right col-price border-bottom"><b>{{ number_format($sellingPrice,0,",",".") }}</b></td>
        </tr>
    </tbody>
</table>
@endif
<br>
<br>
<p><b>Gambar</b></p>

<table class="gema" width="100%">
    <tbody>
        {{ $index = 1 }}
        @foreach($file as $f)
            @if ($index == 1)
                <tr>
            @endif
                <td>
                    <img style="width: 230px" src="{{ $f['image'] }}">
                </td>
            @if ($index == 3)
                </tr>
            @endif

            {{ $index++ }}
            @if ($index > 3)
                {{ $index = 1 }}
            @endif
        @endforeach
    </tbody>
</table>
@endsection
