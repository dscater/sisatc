<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Utilidad Bruta</title>
    <style type="text/css">
        * {
            font-family: sans-serif;
        }

        @page {
            margin-top: 1cm;
            margin-bottom: 0.3cm;
            margin-left: 1.3cm;
            margin-right: 0.3cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
            page-break-before: avoid;
        }

        table thead tr th,
        tbody tr td {
            padding: 3px;
            word-wrap: break-word;
        }

        table thead tr th {
            font-size: 9pt;
        }

        table tbody tr td {
            font-size: 8pt;
        }


        .encabezado {
            width: 100%;
        }

        .logo img {
            position: absolute;
            height: 90px;
            top: -20px;
            left: 0px;
        }

        h2.titulo {
            width: 450px;
            margin: auto;
            margin-top: 0PX;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14pt;
        }

        .texto {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 250px;
            text-align: center;
            margin: auto;
            margin-top: 15px;
            font-weight: normal;
            font-size: 0.85em;
        }

        .total {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }

        table {
            width: 100%;
        }

        table thead {
            background: rgb(236, 236, 236)
        }

        tr {
            page-break-inside: avoid !important;
        }

        .centreado {
            padding-left: 0px;
            text-align: center;
        }

        .datos {
            margin-left: 15px;
            border-top: solid 1px;
            border-collapse: collapse;
            width: 250px;
        }

        .txt {
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
        }

        .txt_center {
            font-weight: bold;
            text-align: center;
        }

        .b_top {
            border-top: solid 1px black;
        }

        .gray {
            background: rgb(202, 202, 202);
        }

        .bg-principal {
            background: #153f59;
            color: white;
        }

        .bold {
            font-weight: bold;
        }

        .text-md {
            font-size: 10pt;
        }

        .text {
            font-size: 9pt;
        }
    </style>
</head>

<body>
    @inject('configuracion', 'App\Models\Configuracion')
    <div class="encabezado">
        <div class="logo">
            <img src="{{ $configuracion->first()->logo_b64 }}">
        </div>
        <h2 class="titulo">
            {{ $configuracion->first()->razon_social }}
        </h2>
        <h4 class="texto">UTILIDAD BRUTA POR PRODUCTO</h4>
        <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
        <h4 class="fecha">{{ $textoFecha }}</h4>
    </div>

    <table border="1">
        <thead class="bg-principal">
            <tr>
                <th>PRODUCTO</th>
                <th>PRESENTACIÓN</th>
                <th>PEDIDO BS.</th>
                <th>ENTREGADO BS.</th>
                <th>DEVUELTO BS.</th>
                <th>TOTAL VENTAS BS.</th>
                <th>COMPRA BS.</th>
                <th>UTILIDAD BS.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $cont = 1;
                $total1 = 0;
                $total2 = 0;
                $total3 = 0;
                $total4 = 0;
                $total5 = 0;
                $total6 = 0;
            @endphp
            @foreach ($productos as $key => $item)
                @foreach ($item->presentacions as $key_presentacion => $presentacion)
                    <tr>
                        @if ($key_presentacion == 0)
                            <td class="centreado" rowspan={{ count($item->presentacions) }}>{{ $item->nombre }}</td>
                        @endif
                        <td class="centreado">{{ $presentacion->nombre }}</td>
                        <td class="centreado">Bs. {{ number_format($presentacion->pedido, 2, '.', ',') }}</td>
                        <td class="centreado">Bs. {{ number_format($presentacion->entregado, 2, '.', ',') }}</td>
                        <td class="centreado">Bs. {{ number_format($presentacion->devolucion, 2, '.', ',') }}</td>
                        @if ($key_presentacion == 0)
                            <td class="centreado" rowspan={{ count($item->presentacions) }}>
                                Bs. {{ number_format($item->total_ventas, 2, '.', ',') }}
                            <td class="centreado" rowspan={{ count($item->presentacions) }}>
                                Bs. {{ number_format($item->costo_compra, 2, '.', ',') }}
                            <td class="centreado" rowspan={{ count($item->presentacions) }}>
                                Bs. {{ number_format($item->utilidad_bruta, 2, '.', ',') }}
                            </td>
                        @endif
                    </tr>
                    @php
                        $total1 += (float) $presentacion->pedido;
                        $total2 += (float) $presentacion->entregado;
                        $total3 += (float) $presentacion->devolucion;
                    @endphp
                @endforeach
                @php
                    $total4 += (float) $item->total_ventas;
                    $total5 += (float) $item->costo_compra;
                    $total6 += (float) $item->utilidad_bruta;
                @endphp
            @endforeach
            <tr>
                <td colspan="2" class="bold text-md">TOTAL GENERAL</td>
                <td class="centreado bold text-md">Bs. {{ number_format($total1, 2, '.', ',') }}</td>
                <td class="centreado bold text-md">Bs. {{ number_format($total2, 2, '.', ',') }}</td>
                <td class="centreado bold text-md">Bs. {{ number_format($total3, 2, '.', ',') }}</td>
                <td class="centreado bold text-md">Bs. {{ number_format($total4, 2, '.', ',') }}</td>
                <td class="centreado bold text-md">Bs. {{ number_format($total5, 2, '.', ',') }}</td>
                <td class="centreado bold text-md">Bs. {{ number_format($total6, 2, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
