<html>
    <head>
        <style><?php include(resource_path('/css/pdf.css')); ?></style>
        <style>
            @page {
                margin: 100px 25px;
            }
        </style>
    </head>
    <body>
        @include('theme.partials.header')
        @include('theme.partials.footer')

        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            {{-- <p style="page-break-after: always;"> --}}
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
                    @for ($i = 0; $i <= 200 ; $i++)
                        <tr>
                            <td>{{$i}}.</td>
                            <td>Item {{$i}}</td>
                            <td class="text-right">100kg</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            {{-- </p> --}}
            {{-- <p style="page-break-after: never;">
                Content Page 2
            </p> --}}
        </main>
    </body>
</html>