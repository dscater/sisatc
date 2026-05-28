<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Despacho</title>
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
        <h4 class="texto">Despacho por Producto</h4>
        <h4 class="fecha">Expedido: {{ date('d-m-Y') }}</h4>
    </div>
    <table border="0" style="margin-top: 40px;width:60%;">
        <tr>
            <td><strong>Código Despacho:</strong> {{ $despacho->id }}</td>
        </tr>
        <tr>
            <td><strong>Fecha:</strong> {{ $despacho->fecha_t }} {{ $despacho->hora }}</td>
        </tr>
        <tr>
            <td><strong>Distribuidor:</strong> {{ $despacho->distribuidor->nombre }}</td>
        </tr>
        <tr>
            <td><strong>Observacion:</strong> {{ $despacho->observacion }}</td>
        </tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th class="bg-principal">Producto</th>
                <th class="bg-principal" style="min-width: 140px">
                    Cantidad Pedido
                </th>
                <!-- <th class="bg-principal" style="min-width: 140px">
                                Stock Actual
                            </th> -->
                <th class="bg-principal" style="min-width: 140px">
                    Cantidad Despacho
                </th>
            </tr>
        </thead>
        <tbody>

            @foreach ($categoria_productos as $item)
                <tr>
                    <td colspan="3" class="gray bold">Categoría: {{ $item->nombre }}</td>
                </tr>
                @foreach ($item['productos'] as $producto_categoria)
                    <tr>
                        <td>
                            <span class="fw-bold fs-6 me-1">
                                {{ $producto_categoria->nombre }}</span>
                        </td>
                        <td class="centreado">
                            {{ $producto_categoria->cantidad_total }}
                        </td>
                        <td class="centreado">
                            {{ $producto_categoria->cantidad_despacho }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
