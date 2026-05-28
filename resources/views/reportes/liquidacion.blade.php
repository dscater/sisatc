<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Liquidacion</title>
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

        .img_celda img {
            width: 45px;
        }

        .nueva_pagina {
            page-break-after: always;
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
        <h4 class="texto">LIQUIDACIÓN DE VENTAS</h4>
        <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
        <h4 class="fecha">{{ $textoFecha }}</h4>
    </div>
    @php
        $cont_user = 0;
    @endphp
    @foreach ($users as $user)
        <table>
            <tbody>
                <tr>
                    <td><strong>Nombre: </strong> {{ $user->full_name }}</td>
                </tr>
                <tr>
                    <td><strong>Tipo: </strong> {{ $user->tipo }}</td>
                </tr>
            </tbody>
        </table>
        <table border="1">
            <thead class="bg-principal">
                <tr>
                    <th width="10%">FECHA</th>
                    <th>TOTAL PREVENTA Bs.</th>
                    <th>TOTAL DEVOLUCIÓN Bs.</th>
                    <th>TOTAL VENTA Bs.</th>
                    <th>TOTAL COMISIÓN Bs.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cont = 1;
                @endphp
                @foreach ($user->data as $key => $item)
                    <tr>
                        <td class="">{{ $item['fecha'] }}</td>
                        @if ($item['tipo_dia'] != 'NORMAL')
                            <td class="centreado">{{ $item['tipo_dia'] }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @else
                            @if ((float) $item['total_pedido'] == 0)
                                <td class="centreado">SIN VENTAS</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @else
                                <td class="centreado">Bs. {{ number_format($item['total_pedido'], 2, '.', ',') }}</td>
                                <td class="centreado">Bs. {{ number_format($item['total_devolucion'], 2, '.', ',') }}
                                </td>
                                <td class="centreado">Bs. {{ number_format($item['total_entregado'], 2, '.', ',') }}
                                </td>
                                <td class="centreado">Bs. {{ number_format($item['total_comision'], 2, '.', ',') }}
                                </td>
                            @endif
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $cont_user++;
        @endphp

        @if ($cont_user < count($users))
            <div class="nueva_pagina"></div>
        @endif
    @endforeach
</body>

</html>
