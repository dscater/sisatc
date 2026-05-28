<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Pedido</title>

    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box;
        }

        @page {
            margin: 10px 8px;
        }

        body {
            width: 100%;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .ticket {
            width: 100%;
        }

        .center {
            text-align: center;
        }

        .logo {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }

        .logo img {
            max-width: 120px;
            max-height: 80px;
        }

        .titulo {
            font-size: 15px;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }

        .subtitulo {
            font-size: 10px;
            margin-bottom: 8px;
        }

        .linea {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .info {
            width: 100%;
            margin-bottom: 5px;
        }

        .info td {
            padding: 1px 0;
            vertical-align: top;
        }

        .productos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .productos th {
            border-bottom: 1px dashed #000;
            padding: 4px 2px;
            font-size: 10px;
        }

        .productos td {
            padding: 3px 2px;
            font-size: 10px;
            vertical-align: top;
        }

        .producto-nombre {
            font-weight: bold;
            font-size: 11px;
            padding-top: 5px;
        }

        .right {
            text-align: right;
        }

        .center-text {
            text-align: center;
        }

        .totales {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }

        .totales td {
            padding: 3px 0;
            font-size: 11px;
        }

        .total-final {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 6px 0 !important;
        }

        .footer {
            text-align: center;
            margin-top: 12px;
            font-size: 10px;
        }

        .pagina {
            margin-top: 8px;
            text-align: center;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <div class="ticket">

        {{-- LOGO --}}
        <div class="logo">
            <img src="{{ $configuracion->logo_b64 }}">
        </div>

        {{-- EMPRESA --}}
        <div class="center">
            <div class="titulo">
                {{ $configuracion->nombre_sistema }}
            </div>

            <div class="subtitulo">
                PEDIDO
            </div>
        </div>

        <div class="linea"></div>

        {{-- INFORMACIÓN --}}
        <table class="info">

            <tr>
                <td width="28%"><strong>Nro:</strong></td>
                <td>{{ $pedido->id }}</td>
            </tr>
            <tr>
                <td><strong>Fecha:</strong></td>
                <td>{{ $pedido->fecha_t }}</td>
            </tr>
            <tr>
                <td><strong>Hora:</strong></td>
                <td>{{ $pedido->hora }}</td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong></td>
                <td>{{ $pedido->cliente->nombre }}</td>
            </tr>
            <tr>
                <td><strong>Tipo de Pago:</strong></td>
                <td>CONTADO</td>
            </tr>
            <tr>
                <td><strong>Vendedor:</strong></td>
                <td>{{ $pedido->user_distribucion->full_name }}</td>
            </tr>
            <tr>
                <td><strong>Distribuidor:</strong></td>
                <td>{{ $pedido->distribuidor->full_name }}</td>
            </tr>
        </table>

        <div class="linea"></div>

        {{-- PRODUCTOS --}}
        <table class="productos">
            <thead>
                <tr>
                    <th width="38%">Producto</th>
                    <th width="12%">Cant.</th>
                    {{-- <th width="15%">Unid.</th> --}}
                    <th width="15%">Precio</th>
                    <th width="20%">Subt.</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($pedido->pedido_detalles as $item)
                    <tr>
                        <td class="producto-nombre">
                            {{ $item->producto->nombre }}
                            {{-- <br>
                            <span style="font-size: 9px; font-weight: normal;">
                                {{ $item->presentacion_producto->nombre }}
                            </span> --}}
                        </td>

                        <td class="center-text">
                            {{ number_format($item->cantidad, 0) }}<br />
                            {{ $item->presentacion_producto->nombre }}
                        </td>

                        {{-- <td class="center-text">
                            {{ number_format($item->cantidad_total, 0) }}
                        </td> --}}

                        <td class="right">
                            {{ number_format($item->precio, 2) }}
                        </td>

                        <td class="right">
                            {{ number_format($item->subtotal, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- <div class="linea"></div> --}}

        {{-- TOTALES --}}
        <table class="totales">
            {{-- <tr>
                <td><strong>Subtotal Bs.</strong></td>
                <td class="right">
                    {{ number_format($pedido->subtotal, 2) }}
                </td>
            </tr>

            <tr>
                <td><strong>Descuento Bs.</strong></td>
                <td class="right">
                    {{ number_format($pedido->descuento, 2) }}
                </td>
            </tr> --}}

            <tr>
                <td class="total-final">
                    TOTAL Bs.
                </td>

                <td class="right total-final">
                    {{ number_format($pedido->total, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="center-text">
                    Son: {{ $literal }}
                </td>
            </tr>
        </table>

        <div class="footer">
            Gracias por su compra
        </div>

    </div>

</body>

</html>
