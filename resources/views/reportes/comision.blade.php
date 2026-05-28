<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comisión</title>
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
            text-align: center;
            font-size: 14pt;
        }

        .texto {
            width: 250px;
            text-align: center;
            margin: auto;
            font-weight: bold;
            font-size: 1.1em;
        }

        .fecha {
            width: 250px;
            text-align: center;
            margin: auto;
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

        .txt_center {
            font-weight: bold;
            text-align: center;
        }


        .gray {
            background: rgb(240, 240, 240);
        }

        .bg-principal {
            background: #153f59;
            color: white;
        }

        .img_celda img {
            width: 45px;
        }

        .derecha {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .lista {
            padding-left: 4px;
            margin-left: 8px;
        }

        .text-md {
            font-size: 10pt;
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
        <h4 class="texto">COMISIONES</h4>
        <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
    </div>
    <table border="0" style="margin-top: 40px;width:60%;">
        <tr>
            <td><strong>Código:</strong> {{ $comision->id }}</td>
        </tr>
        <tr>
            <td><strong>Distribuidor:</strong> {{ $comision->distribuidor->nombre }}</td>
        </tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th class="bg-principal">Producto</th>
                <th class="bg-principal">
                    Cantidad Vendidos
                </th>
                <th class="bg-principal">
                    Monto Vendido
                </th>
                <th class="bg-principal">
                    Comisión Generada Distribuidor
                </th>
                <th class="bg-principal derecha">
                    Comisión Generada Vendedor
                </th>
                <th class="bg-principal derecha">
                    Total Pagar Distribuidor
                </th>
                <th class="bg-principal derecha">
                    Total Pagar Vendedor
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $total1 = 0;
                $total2 = 0;
                $total3 = 0;
                $total4 = 0;
            @endphp
            @foreach ($comision_detalles as $detalle)
                <tr>
                    <td colspan="7" class="gray bold">Código Despacho {{ $detalle->despacho_id }}</td>
                </tr>
                @foreach ($detalle['categoria_productos'] as $categoria_producto)
                    @foreach ($categoria_producto['productos'] as $producto_categoria)
                        <tr>
                            <td align="top">
                                <span class="fw-bold fs-6 me-1">
                                    {{ $producto_categoria->nombre }}</span>
                            </td>
                            <td class="text-center">
                                <ul class="lista">
                                    @foreach ($producto_categoria['presentacions'] as $presentacion)
                                        <li class="text-left" v-for="presentacion in producto_categoria.presentacions">
                                            {{ $presentacion['nombre'] }}
                                            ({{ $presentacion['total_cantidad'] }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="derecha">
                                {{ $producto_categoria->monto_vendido }}
                                Bs.
                            </td>
                            <td class="derecha">
                                {{ $producto_categoria->comision_distribuidor }}
                                Bs.
                            </td>
                            <td class="derecha">
                                {{ $producto_categoria->comision_vendedor }}
                                Bs.
                            </td>
                            <td class="derecha">
                                {{ $producto_categoria->entrega_distribuidor }}
                                Bs.
                            </td>
                            <td class="derecha">
                                {{ $producto_categoria->entrega_vendedor }}
                                Bs.
                            </td>
                        </tr>
                        @php
                            $total += (float) $producto_categoria->monto_vendido;
                            $total1 += (float) $producto_categoria->comision_distribuidor;
                            $total2 += (float) $producto_categoria->comision_vendedor;
                            $total3 += (float) $producto_categoria->entrega_distribuidor;
                            $total4 += (float) $producto_categoria->entrega_vendedor;
                        @endphp
                    @endforeach
                @endforeach
            @endforeach
            <tr class="bg-principal">
                <td colspan="2">TOTAL</td>
                <td class="derecha bold text-md">{{ number_format($total, 2, '.', ',') }} Bs.</td>
                <td class="derecha bold text-md">{{ number_format($total1, 2, '.', ',') }} Bs.</td>
                <td class="derecha bold text-md">{{ number_format($total2, 2, '.', ',') }} Bs.</td>
                <td class="derecha bold text-md">{{ number_format($total3, 2, '.', ',') }} Bs.</td>
                <td class="derecha bold text-md">{{ number_format($total4, 2, '.', ',') }} Bs.</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
