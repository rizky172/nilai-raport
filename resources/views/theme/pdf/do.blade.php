@extends('theme.pdf.layout-do')

@section('content')
<style>
/*
p, table td {
    font-size: 12px;
    line-height: 1.2;
}
*/
@page { size: 19cm 14cm; }
</style>

<div class="clearfix" style="margin-bottom: 0;">
    <div style="float: left; width: 49%;">
        <img src="{{URL::asset('/img/gema_logo_h64px.png')}}" height="34" width="auto">
        <h1 style="margin: 0 0 10px 0;">Delivery Order</h1>

        <table style="width:100%; margin-bottom: 10px;">
            <tr>
                <td width="5%"><p class="title">Tanggal</p></td>
                <td width="15%"><p class="title">: 20 April 2020</p></td>
                <td width="20%" align="right"><p class="title">PR/000001/02.20</p></td>
            </tr>
            <tr>
                <td width="5%"><p class="title">Tujuan/Line</p></td>
                <td width="15%"><p class="title">: SCHOTT</p></td>
                <td width="20%" align="right"><p class="title">20 April 2020</p></td>
            </tr>
        </table>

        <table  style="width:100%;" class="gema">
            <thead>
                <tr>
                    <th class="text-left" width="25px">No</th>
                    <th class="text-left">Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-center">WH</th>
                    <th class="text-center">QC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>Item A</td>
                    <td class="text-right">100kg</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>99.</td>
                    <td>Item A</td>
                    <td class="text-right">100kg</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div class="clearfix">
            <div style="float: left; width:60%;">
                <p><b>Notes</b></p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec vitae purus risus. </p>
            </div>
            <table style="float: right; width: 40%;" cellspacing="0" cellpadding="0">
                <tbody>
                  
                    <tr>
                        <td><br><br></td>
                    </tr>
                    <tr>
                        <td class="text-center"><u>Penanggung Jawab</u></td>
                    </tr>
                    <tr>
                        <td class="text-center"><strong>Jabatan</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div style="float: right; width: 49%;">
        <img src="{{URL::asset('/img/gema_logo_h64px.png')}}" height="34" width="auto">
        <h1 style="margin: 0 0 10px 0;">Delivery Order</h1>

        <table style="width:100%; margin-bottom: 10px;">
            <tr>
                <td width="5%"><p class="title">Tanggal</p></td>
                <td width="15%"><p class="title">: 20 April 2020</p></td>
                <td width="20%" align="right"><p class="title">PR/000001/02.20</p></td>
            </tr>
            <tr>
                <td width="5%"><p class="title">Tujuan/Line</p></td>
                <td width="15%"><p class="title">: SCHOTT</p></td>
                <td width="20%" align="right"><p class="title">20 April 2020</p></td>
            </tr>
        </table>

        <table  style="width:100%;" class="gema">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">WH</th>
                    <th class="text-center">QC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td>Item A</td>
                    <td class="text-right">100kg</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div class="clearfix">
            <div style="float: left; width: 60%;">
                <p><b>Notes</b></p>
                <p></p>
            </div>
            <table style="float: right; width: 40%;" cellspacing="0" cellpadding="0">
                <tbody>
                  
                    <tr>
                        <td><br><br></td>
                    </tr>
                    <tr>
                        <td class="text-center"><u>Penanggung Jawab</u></td>
                    </tr>
                    <tr>
                        <td class="text-center"><strong>Jabatan</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- <div>
        <p>Jl. Rawa Bogo No.123 RT.001/003 Kel Padurenan Kec.Mustika Jaya</p>
        <p>Telp.021-8262-3919 / 021-8262-3311 Email: <a href="mailto:info@gemaputraabadi.com">info@gemaputraabadi.com</a></p>
        <p>Email: <a href="mailto:tika@gemaputraabadi.com">tika@gemaputraabadi.com</a></p>
        <div class="border-outline wd50">
            <p><b>NPWP  31.341.750.3-432.000</b></p>
        </div>
    </div> -->
</div>
@endsection
