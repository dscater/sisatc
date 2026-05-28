<?php

namespace App\Http\Controllers;

use App\Models\CertificadoDetalle;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Configuracion;
use App\Models\HistorialAccion;
use App\Models\MovimientoInventario;
use App\Models\Pago;
use App\Models\PedidoDetalle;
use App\Models\PresentacionProducto;
use App\Models\Producto;
use App\Models\TipoCertificado;
use App\Models\User;
use App\Services\ReporteService;
use App\Services\ReporteServiceTcpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PDF;
use Carbon\Carbon;
use FPDF;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ReporteController extends Controller
{
    public $titulo = [
        'font' => [
            'bold' => true,
            'size' => 12,
            'family' => 'Times New Roman'
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
            ],
        ],
    ];

    public $textoBold = [
        'font' => [
            'bold' => true,
            'size' => 10,
        ],
    ];

    public $headerTabla = [
        'font' => [
            'bold' => true,
            'size' => 10,
            'color' => ['argb' => 'ffffff'],
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => '203764']
        ],
    ];

    public $headerTablaRed = [
        'font' => [
            'bold' => true,
            'size' => 10,
            'color' => ['argb' => 'ffffff'],
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'f02222']
        ],
    ];

    public $headerTabla2 = [
        'font' => [
            'bold' => true,
            'size' => 10,
            'color' => ['argb' => '000000'],
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    public $bodyTabla = [
        'font' => [
            'size' => 10,
        ],
        'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            // 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    public $textLeft = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
    ];

    public $textRight = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ],
    ];

    public $textCenter = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];

    public $bg0 = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'cff3f3']
        ],
    ];

    public $bg1 = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'ffe9ff']
        ],
    ];

    public $bg2 = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'f7ffe0']
        ],
    ];

    public $bg3 = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'ecfcdd']
        ],
    ];

    public $bg4 = [
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'color' => ['rgb' => 'faeee4']
        ],
    ];

    private $configuracion = null;
    public function __construct()
    {
        $this->configuracion = Configuracion::first();
        if (!$this->configuracion) {
            $this->configuracion = new Configuracion([
                "nombre_sistema" => "MEDINTER S.A.",
                "alias" => "MD",
                "logo" => "logo.png",
                "fono" => "2222222",
                "dir" => "LOS OLIVOS",
            ]);
        }
    }

    public function usuarios()
    {
        return Inertia::render("Admin/Reportes/Usuarios");
    }

    public function r_usuarios(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);
        $tipo =  $request->tipo;
        $formato =  $request->formato;
        $usuarios = User::select("users.*")
            ->where('id', '!=', 1);

        if ($tipo != 'todos') {
            $request->validate([
                'tipo' => 'required',
            ]);
            $usuarios->where('tipo', $tipo);
        }

        $usuarios = $usuarios->get();

        if ($formato == 'pdf') {
            $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('usuarios.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":F" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':F' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE USUARIOS");
            $sheet->mergeCells("A" . $fila . ":F" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':F' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'USUARIO');
            $sheet->setCellValue('C' . $fila, 'NOMBRE');
            $sheet->setCellValue('D' . $fila, 'TIPO');
            $sheet->setCellValue('E' . $fila, 'BLOQUEO');
            $sheet->setCellValue('F' . $fila, 'ACCESO');
            $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($usuarios as $key => $item) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $item->usuario);
                $sheet->setCellValue('C' . $fila, $item->nombre);
                $sheet->setCellValue('D' . $fila, $item->tipo);
                $sheet->setCellValue('E' . $fila, $item->bloqueo == 1 ? 'HABILITADO' : 'DENEGADO');
                $sheet->setCellValue('F' . $fila, $item->acceso == 1 ? 'HABILITADO' : 'DENEGADO');
                $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);

            foreach (range('A', 'F') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:F');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                'usuarios_' . time() . '.xlsx',
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }
    }

    public function productos()
    {
        return Inertia::render("Admin/Reportes/Productos");
    }

    public function r_productos(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);
        $categoria_producto_id =  $request->categoria_producto_id;
        $formato =  $request->formato;
        $productos = Producto::select("productos.*");

        if ($categoria_producto_id != 'todos') {
            $productos->where('categoria_producto_id', $categoria_producto_id);
        }

        $productos = $productos->get();

        if ($formato == 'pdf') {
            $pdf = PDF::loadView('reportes.productos', compact('productos'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('productos.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "LISTA DE PRODUCTOS");
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'NOMBRE');
            $sheet->setCellValue('C' . $fila, 'DESCRIPCION');
            $sheet->setCellValue('D' . $fila, 'CATEGORIA');
            $sheet->setCellValue('E' . $fila, 'PRESENTACION');
            $sheet->setCellValue('F' . $fila, 'STOCK MIN.');
            $sheet->setCellValue('G' . $fila, 'STOCK ACTUAL');
            $sheet->setCellValue('H' . $fila, 'ESTADO');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($productos as $key => $item) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $item->nombre);
                $sheet->setCellValue('C' . $fila, $item->descripcion);
                $sheet->setCellValue('D' . $fila, $item->categoria_producto->nombre);
                $list_presentacions = "";
                foreach ($item->presentacion_productos as $presentacion) {
                    $list_presentacions .= $presentacion->nombre . " ($presentacion->equivale) \n";
                }
                $sheet->setCellValue('E' . $fila, $list_presentacions);
                $sheet->setCellValue('F' . $fila, $item->stock_min);
                $sheet->setCellValue('G' . $fila, $item->stock_actual);
                $sheet->setCellValue('H' . $fila, $item->estado == 1 ? 'HABILITADO' : 'DESHABILITADO');
                $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);

            foreach (range('A', 'H') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:H');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                'productos_' . time() . '.xlsx',
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }
    }

    public function movimiento_inventarios()
    {
        return Inertia::render("Admin/Reportes/MovimientoInventarios");
    }

    public function r_movimiento_inventarios(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);
        $categoria_producto_id =  $request->categoria_producto_id;
        $producto_id =  $request->producto_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $formato =  $request->formato;
        $movimiento_inventarios = MovimientoInventario::select("movimiento_inventarios.*");

        if ($categoria_producto_id != 'todos') {
            $movimiento_inventarios->whereHas('producto', function ($q) use ($categoria_producto_id) {
                $q->where('categoria_producto_id', $categoria_producto_id);
            });
        }
        if ($producto_id != 'todos') {
            $movimiento_inventarios->where('producto_id', $producto_id);
        }
        if ($fecha_ini && $fecha_fin) {
            $movimiento_inventarios->whereBetween('fecha', [$fecha_ini, $fecha_fin]);
        }
        $movimiento_inventarios = $movimiento_inventarios->get();

        if ($formato == 'pdf') {
            $pdf = PDF::loadView('reportes.movimiento_inventarios', compact('movimiento_inventarios'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('movimiento_inventarios.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":G" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':G' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':G' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "MOVIMIENTO DE INVENTARIO");
            $sheet->mergeCells("A" . $fila . ":G" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':G' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':G' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'N°');
            $sheet->setCellValue('B' . $fila, 'FECHA');
            $sheet->setCellValue('C' . $fila, 'PRODUCTO');
            $sheet->setCellValue('D' . $fila, 'DETALLE');
            $sheet->setCellValue('E' . $fila, 'CANTIDAD INGRESO');
            $sheet->setCellValue('F' . $fila, 'CANTIDAD SALIDA');
            $sheet->setCellValue('G' . $fila, 'SALDO');
            $sheet->getStyle('A' . $fila . ':G' . $fila)->applyFromArray($this->headerTabla);
            $fila++;

            foreach ($movimiento_inventarios as $key => $item) {
                $sheet->setCellValue('A' . $fila, $key + 1);
                $sheet->setCellValue('B' . $fila, $item->fecha_t);
                $sheet->setCellValue('C' . $fila, $item->producto->nombre);
                $sheet->setCellValue('D' . $fila, $item->detalle ? $item->detalle : $item->tipo_registro);
                $sheet->setCellValue('E' . $fila, $item->cantidad_ingreso ?? '');
                $sheet->setCellValue('F' . $fila, $item->cantidad_salida ?? '');
                $sheet->setCellValue('G' . $fila, $item->cantidad_saldo);
                $sheet->getStyle('A' . $fila . ':G' . $fila)->applyFromArray($this->bodyTabla);
                $fila++;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);

            foreach (range('A', 'G') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:G');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                'movimiento_inventarios_' . time() . '.xlsx',
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }
    }

    public function liquidacion()
    {
        return Inertia::render("Admin/Reportes/Liquidacion");
    }

    public function r_liquidacion(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);
        $user_id =  $request->user_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $formato =  $request->formato;

        $textoFecha = $this->getTextoFecha($fecha_ini, $fecha_fin);
        $users = User::select("users.*");
        if ($user_id != 'todos') {
            $users->where("id", $user_id);
        } else {
            $users->whereIn("tipo", ["DISTRIBUIDOR", "VENDEDOR"]);
        }

        $fecha_final = Carbon::parse($fecha_fin);
        $users = $users->get()
            ->map(function ($user) use ($fecha_ini, $fecha_final) {
                $user->permisos = [];
                $fecha_actual = Carbon::parse($fecha_ini);
                while ($fecha_actual <= $fecha_final) {

                    $tipo_dia = "NORMAL";

                    if ($fecha_actual->isSunday()) {
                        $tipo_dia = "DOMINGO";
                    }

                    if ($fecha_actual->isSaturday()) {
                        $tipo_dia = "SÁBADO";
                    }

                    $total_pedido = 0;
                    $total_entregado = 0;
                    $total_devolucion = 0;
                    $total_comision = 0;

                    $presentacions = PresentacionProducto::whereHas("pedido_detalles", function ($q) use ($user, $fecha_actual) {
                        $q->whereHas("pedido", function ($sub) use ($user, $fecha_actual) {
                            $sub->where("estado", "ENTREGADO");
                            $sub->where("fecha", $fecha_actual);
                            if ($user != 'todos') {
                                $sub->where(function ($sub2) use ($user) {
                                    $sub2->where("distribuidor_id", $user->id);
                                    $sub2->orWhere("user_distribucion_id", $user->id);
                                });
                            }
                        });
                    })->distinct()
                        ->get()->map(function ($presentacion) use ($user, $fecha_actual, &$total_pedido, &$total_entregado, &$total_devolucion, &$total_comision) {
                            $presentacion->cantidad_despacho = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_actual, $user, $presentacion) {
                                $q->where("estado", "ENTREGADO");
                                $q->where("fecha", $fecha_actual);
                                $q->where("presentacion_producto_id", $presentacion->id);
                                if ($user != 'todos') {
                                    $q->where(function ($sub) use ($user) {
                                        $sub->where("distribuidor_id", $user->id);
                                        $sub->orWhere("user_distribucion_id", $user->id);
                                    });
                                }
                            })->sum("cantidad_despacho");
                            $presentacion->cantidad_entregado = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_actual, $user, $presentacion) {
                                $q->where("estado", "ENTREGADO");
                                $q->where("fecha", $fecha_actual);
                                $q->where("presentacion_producto_id", $presentacion->id);
                                if ($user != 'todos') {
                                    $q->where(function ($sub) use ($user) {
                                        $sub->where("distribuidor_id", $user->id);
                                        $sub->orWhere("user_distribucion_id", $user->id);
                                    });
                                }
                            })->sum("cantidad_entregado");
                            $presentacion->cantidad_devolucion = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_actual, $user, $presentacion) {
                                $q->where("estado", "ENTREGADO");
                                $q->where("fecha", $fecha_actual);
                                $q->where("presentacion_producto_id", $presentacion->id);
                                if ($user != 'todos') {
                                    $q->where(function ($sub) use ($user) {
                                        $sub->where("distribuidor_id", $user->id);
                                        $sub->orWhere("user_distribucion_id", $user->id);
                                    });
                                }
                            })->sum("cantidad_devolucion");

                            $cantidad = round($presentacion->cantidad_despacho / $presentacion->equivale, 2);
                            $cantidad_entregado = round($presentacion->cantidad_entregado / $presentacion->equivale, 2);
                            $cantidad_devolucion = round($presentacion->cantidad_devolucion / $presentacion->equivale, 2);
                            // pedido
                            $presentacion->pedido = $presentacion->precio * $cantidad;
                            // entregado
                            $presentacion->entregado = $presentacion->precio * $cantidad_entregado;
                            // devolucion
                            $presentacion->devolucion = $presentacion->precio * $cantidad_devolucion;

                            // comision
                            if ($user->tipo == 'DISTRIBUIDOR') {
                                $presentacion->comision = round(($cantidad_entregado * $presentacion->precio) * ($presentacion->comi_distribuidor / 100), 2);
                            } else {
                                $presentacion->comision = round(($cantidad_entregado * $presentacion->precio) * ($presentacion->comi_vendedor / 100), 2);
                                // Log::debug($cantidad_entregado);
                                // Log::debug($presentacion->comi_vendedor);
                                // Log::debug($presentacion->comision);
                                // Log::debug("................................");
                            }

                            $total_pedido += (float)$presentacion->pedido;
                            $total_entregado += (float)$presentacion->entregado;
                            $total_devolucion += (float)$presentacion->devolucion;
                            $total_comision += (float)$presentacion->comision;

                            return $presentacion;
                        });

                    $fecha_actual->addDay();

                    $data[] = [
                        "fecha" => $fecha_actual->format("Y-m-d"),
                        "tipo_dia" => $tipo_dia,
                        "presentacions" => $presentacions,
                        "total_pedido" => $total_pedido,
                        "total_entregado" => $total_entregado,
                        "total_devolucion" => $total_devolucion,
                        "total_comision" => $total_comision,
                    ];
                }
                $user->data = $data;

                return $user;
            });

        if ($formato == 'pdf') {
            $pdf = PDF::loadView('reportes.liquidacion', compact('users', 'textoFecha'))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('liquidacion.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":E" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':E' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "MOVIMIENTO DE INVENTARIO");
            $sheet->mergeCells("A" . $fila . ":E" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':E' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, $textoFecha);
            $sheet->mergeCells("A" . $fila . ":E" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':E' . $fila)->getAlignment()->setHorizontal('center');
            $fila++;
            $fila++;


            foreach ($users as $user) {
                $sheet->setCellValue('A' . $fila, 'NOMBRE:');
                $sheet->mergeCells("A" . $fila . ":B" . $fila);  //COMBINAR CELDAS
                $sheet->setCellValue('C' . $fila, $user->full_name);
                $sheet->mergeCells("C" . $fila . ":E" . $fila);  //COMBINAR CELDAS
                $fila++;

                $sheet->setCellValue('A' . $fila, 'TIPO:');
                $sheet->mergeCells("A" . $fila . ":B" . $fila);  //COMBINAR CELDAS
                $sheet->setCellValue('C' . $fila, $user->tipo);
                $sheet->mergeCells("C" . $fila . ":E" . $fila);  //COMBINAR CELDAS
                $fila++;

                $sheet->setCellValue('A' . $fila, 'FECHA');
                $sheet->setCellValue('B' . $fila, 'TOTAL PREVENTA Bs.');
                $sheet->setCellValue('C' . $fila, 'TOTAL DEVOLUCIÓN Bs.');
                $sheet->setCellValue('D' . $fila, 'TOTAL VENTA Bs.');
                $sheet->setCellValue('E' . $fila, 'TOTAL COMISIÓN Bs.');
                $sheet->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($this->headerTabla);
                $fila++;

                foreach ($user->data as $key => $item) {
                    $sheet->setCellValue('A' . $fila, $item["fecha"]);
                    if ($item["tipo_dia"] != 'NORMAL') {
                        $sheet->setCellValue('B' . $fila, $item["tipo_dia"]);
                    } else {
                        if ((float)$item["total_pedido"] == 0) {
                            $sheet->setCellValue('B' . $fila, "SIN VENTAS");
                        } else {
                            $sheet->setCellValue('B' . $fila, 'Bs. ' . number_format($item["total_pedido"], 2, ".", ","));
                            $sheet->setCellValue('C' . $fila, 'Bs. ' . number_format($item["total_devolucion"], 2, ".", ","));
                            $sheet->setCellValue('D' . $fila, 'Bs. ' . number_format($item["total_entregado"], 2, ".", ","));
                            $sheet->setCellValue('E' . $fila, 'Bs. ' . number_format($item["total_comision"], 2, ".", ","));
                        }
                    }

                    $sheet->getStyle('A' . $fila . ':E' . $fila)->applyFromArray($this->bodyTabla);
                    $fila++;
                }
                $fila += 3;
            }

            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);

            foreach (range('A', 'G') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:E');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                'liquidacion_' . time() . '.xlsx',
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }
    }

    public function utilidad_bruta()
    {
        return Inertia::render("Admin/Reportes/UtilidadBruta");
    }

    public function r_utilidad_bruta(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);
        $categoria_producto_id =  $request->categoria_producto_id;
        $producto_id =  $request->producto_id;
        $fecha_ini =  $request->fecha_ini;
        $fecha_fin =  $request->fecha_fin;
        $formato =  $request->formato;
        $textoFecha = $this->getTextoFecha($fecha_ini, $fecha_fin);

        $productos = Producto::select("productos.*");

        if ($categoria_producto_id != 'todos') {
            $productos->where("categoria_producto_id", $categoria_producto_id);
        }

        if ($producto_id != 'todos') {
            $productos->where("id", $producto_id);
        }

        $productos = $productos->orderBy("nombre")
            ->get()->map(function ($producto) use ($fecha_ini, $fecha_fin) {

                $total_ventas = 0;
                $total_unidades = 0;
                $costo_compra = 0;

                $producto->presentacions = PresentacionProducto::where("producto_id", $producto->id)
                    ->get()
                    ->map(function ($presentacion) use ($producto, $fecha_ini, $fecha_fin, &$total_ventas, &$total_unidades) {
                        $presentacion->cantidad_total = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_ini, $fecha_fin, $producto, $presentacion) {
                            $q->where("estado", "ENTREGADO");
                            $q->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
                            $q->where("producto_id", $producto->id);
                            $q->where("presentacion_producto_id", $presentacion->id);
                        })->sum("cantidad_total");
                        $presentacion->cantidad_despacho = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_ini, $fecha_fin, $producto, $presentacion) {
                            $q->where("estado", "ENTREGADO");
                            $q->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
                            $q->where("producto_id", $producto->id);
                            $q->where("presentacion_producto_id", $presentacion->id);
                        })->sum("cantidad_despacho");
                        $presentacion->cantidad_entregado = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_ini, $fecha_fin, $producto, $presentacion) {
                            $q->where("estado", "ENTREGADO");
                            $q->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
                            $q->where("producto_id", $producto->id);
                            $q->where("presentacion_producto_id", $presentacion->id);
                        })->sum("cantidad_entregado");
                        $presentacion->cantidad_devolucion = PedidoDetalle::whereHas("pedido", function ($q) use ($fecha_ini, $fecha_fin, $producto, $presentacion) {
                            $q->where("estado", "ENTREGADO");
                            $q->whereBetween("fecha", [$fecha_ini, $fecha_fin]);
                            $q->where("producto_id", $producto->id);
                            $q->where("presentacion_producto_id", $presentacion->id);
                        })->sum("cantidad_devolucion");

                        $cantidad = round($presentacion->cantidad_despacho / $presentacion->equivale, 2);
                        $cantidad_entregado = round($presentacion->cantidad_entregado / $presentacion->equivale, 2);
                        $cantidad_devolucion = round($presentacion->cantidad_devolucion / $presentacion->equivale, 2);
                        // pedido
                        $presentacion->pedido = $presentacion->precio * $cantidad;
                        // entregado
                        $presentacion->entregado = $presentacion->precio * $cantidad_entregado;
                        // devolucion
                        $presentacion->devolucion = $presentacion->precio * $cantidad_devolucion;

                        $total_ventas += (float)$presentacion->entregado;

                        $total_unidades += $presentacion->cantidad_entregado;

                        return $presentacion;
                    });

                $producto->total_ventas = $total_ventas;
                $count_compras = Compra::where("producto_id", $producto->id)->count();
                $suma_compras = Compra::where("producto_id", $producto->id)->sum("precio_compra");
                $costo_compra = round($suma_compras / $count_compras, 2);
                // Log::debug($count_compras);
                // Log::debug($suma_compras);
                // Log::debug($costo_compra);
                // Log::debug($total_unidades);
                // Log::debug("------------");
                $producto->costo_compra = round($costo_compra * $total_unidades, 2);
                $producto->utilidad_bruta = round((float)$costo_compra - (float)$total_ventas, 2);

                return $producto;
            });

        if ($formato == 'pdf') {
            $pdf = PDF::loadView('reportes.utilidad_bruta', compact('productos', "textoFecha"))->setPaper('legal', 'portrait');

            // ENUMERAR LAS PÁGINAS USANDO CANVAS
            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $alto = $canvas->get_height();
            $ancho = $canvas->get_width();
            $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

            return $pdf->stream('utilidad_bruta.pdf');
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()
                ->setCreator("ADMIN")
                ->setLastModifiedBy('Administración')
                ->setTitle('Registros')
                ->setSubject('Registros')
                ->setDescription('Registros')
                ->setKeywords('PHPSpreadsheet')
                ->setCategory('Listado');

            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

            $fila = 1;
            if (file_exists(public_path() . '/imgs/' . $this->configuracion->logo)) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('logo');
                $drawing->setDescription('logo');
                $drawing->setPath(public_path() . '/imgs/' . $this->configuracion->logo); // put your path and image here
                $drawing->setCoordinates('A' . $fila);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(0);
                $drawing->setHeight(70);
                $drawing->setWorksheet($sheet);
            }

            $fila = 2;
            $sheet->setCellValue('A' . $fila, $this->configuracion->nombre_sistema);
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $sheet->setCellValue('A' . $fila, "MOVIMIENTO DE INVENTARIO");
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->titulo);
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, $textoFecha);
            $sheet->mergeCells("A" . $fila . ":H" . $fila);  //COMBINAR CELDAS
            $sheet->getStyle('A' . $fila . ':H' . $fila)->getAlignment()->setHorizontal('center');
            $fila++;
            $fila++;
            $sheet->setCellValue('A' . $fila, 'PRODUCTO');
            $sheet->setCellValue('B' . $fila, 'PRESENTACIÓN');
            $sheet->setCellValue('C' . $fila, 'PEDIDO BS.');
            $sheet->setCellValue('D' . $fila, 'ENTREGADO BS.');
            $sheet->setCellValue('E' . $fila, 'DEVUELTO BS.');
            $sheet->setCellValue('F' . $fila, 'TOTAL VENTAS BS.');
            $sheet->setCellValue('G' . $fila, 'COMPRA BS.');
            $sheet->setCellValue('h' . $fila, 'UTILIDAD BS.');
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->headerTabla);
            $fila++;


            $total1 = 0;
            $total2 = 0;
            $total3 = 0;
            $total4 = 0;
            $total5 = 0;
            $total6 = 0;
            foreach ($productos as $key => $item) {
                $sheet->setCellValue('A' . $fila, $item->nombre);
                $sheet->mergeCells("A" . $fila . ":A" . ($fila + count($item->presentacions) - 1));  //COMBINAR CELDAS
                $sheet->setCellValue('F' . $fila, $item->total_ventas);
                $sheet->mergeCells("F" . $fila . ":F" . ($fila + count($item->presentacions) - 1));  //COMBINAR CELDAS
                $sheet->setCellValue('G' . $fila, $item->costo_compra);
                $sheet->mergeCells("G" . $fila . ":G" . ($fila + count($item->presentacions) - 1));  //COMBINAR CELDAS
                $sheet->setCellValue('H' . $fila, $item->utilidad_bruta);
                $sheet->mergeCells("H" . $fila . ":H" . ($fila + count($item->presentacions) - 1));  //COMBINAR CELDAS

                foreach ($item->presentacions as $key_presentacion => $presentacion) {
                    $sheet->setCellValue('B' . $fila, $presentacion->nombre);
                    $sheet->setCellValue('C' . $fila, number_format($presentacion->pedido, 2, ".", ","));
                    $sheet->setCellValue('D' . $fila, number_format($presentacion->entregado, 2, ".", ","));
                    $sheet->setCellValue('E' . $fila, number_format($presentacion->devolucion, 2, ".", ","));
                    $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->bodyTabla);

                    $total1 += (float) $presentacion->pedido;
                    $total2 += (float) $presentacion->entregado;
                    $total3 += (float) $presentacion->devolucion;

                    $fila++;
                }

                $total4 += (float) $item->total_ventas;
                $total5 += (float) $item->costo_compra;
                $total6 += (float) $item->utilidad_bruta;
            }

            $sheet->setCellValue('A' . $fila, "TOTAL GENERAL");
            $sheet->mergeCells("A" . $fila . ":B" . $fila);  //COMBINAR CELDAS
            $sheet->setCellValue('C' . $fila, number_format($total1, 2, '.', ','));
            $sheet->setCellValue('D' . $fila, number_format($total2, 2, '.', ','));
            $sheet->setCellValue('E' . $fila, number_format($total3, 2, '.', ','));
            $sheet->setCellValue('F' . $fila, number_format($total4, 2, '.', ','));
            $sheet->setCellValue('G' . $fila, number_format($total5, 2, '.', ','));
            $sheet->setCellValue('H' . $fila, number_format($total6, 2, '.', ','));
            $sheet->getStyle('A' . $fila . ':H' . $fila)->applyFromArray($this->headerTabla);

            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);

            foreach (range('A', 'H') as $columnID) {
                $sheet->getStyle($columnID)->getAlignment()->setWrapText(true);
            }

            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.1);
            $sheet->getPageMargins()->setLeft(0.1);
            $sheet->getPageMargins()->setBottom(0.1);
            $sheet->getPageSetup()->setPrintArea('A:H');
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            return response()->streamDownload(
                function () use ($spreadsheet) {
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save('php://output');
                },
                'utilidad_bruta_' . time() . '.xlsx',
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
        }
    }

    public function getTextoFecha($fecha_ini, $fecha_fin)
    {
        if (!$fecha_ini || !$fecha_fin) {
            return "";
        }

        $fecha_ini = Carbon::parse($fecha_ini);
        $fecha_fin = Carbon::parse($fecha_fin);

        $meses = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        if ($fecha_ini->year == $fecha_fin->year) {

            $texto = "Del "
                . $fecha_ini->format('d')
                . " de " . $meses[$fecha_ini->month]
                . " al "
                . $fecha_fin->format('d')
                . " de " . $meses[$fecha_fin->month]
                . " de "
                . $fecha_fin->year;
        } else {

            $texto = "Del "
                . $fecha_ini->format('d')
                . " de " . $meses[$fecha_ini->month]
                . " de " . $fecha_ini->year
                . " al "
                . $fecha_fin->format('d')
                . " de " . $meses[$fecha_fin->month]
                . " de " . $fecha_fin->year;
        }

        return $texto;
    }
}
