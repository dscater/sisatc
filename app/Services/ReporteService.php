<?php

namespace App\Services;

use App\Models\Configuracion;
use FPDF;

class ReporteService extends FPDF
{
    protected $titulo = "";
    protected $conFecha = "";
    public function __construct($titulo = "", $conFecha = false)
    {
        parent::__construct();
        $this->titulo = $titulo;
        $this->conFecha = $conFecha;
    }

    // Encabezado
    public function Header()
    {
        if ($this->PageNo() == 1) {
            $configuracion = Configuracion::first();
            $path_foto = public_path("imgs/" . $configuracion->logo);

            if (file_exists($path_foto)) {
                // x, y, ancho (altura se ajusta automáticamente)
                $this->Image($path_foto, 10, 5, 25);
            }

            $this->Ln(7); // Espacio debajo de la imagen
            $this->SetFont('Arial', 'B', 12);
            $this->CellUtf8(0, 5, $configuracion->nombre_sistema, 0, 1, 'C');
            if ($this->titulo) {
                $this->CellUtf8(0, $this->conFecha ? 5 : 0, $this->titulo, 0, 1, 'C');
            }

            if ($this->conFecha) {
                $this->SetFont('Arial', 'B', 10);
                $this->CellUtf8(0, 5, "Expedido: " . date("d/m/Y"), 0, 1, 'C');
            }
        } else {
            // Header para otras páginas
            $this->SetFont('Arial', 'B', 10);
            // $this->Cell(0, 10, 'Encabezado de otras páginas', 0, 1, 'C');
        }
    }

    // Pie de página
    public function Footer()
    {
        $this->SetY(-15); // 15 mm desde el fondo
        $this->SetFont('Arial', 'B', 10);

        $pageNumber = $this->PageNo();
        $totalPages = '{nb}'; // Reemplazará con total de páginas al llamar AliasNbPages

        $this->Cell(0, 10, utf8_decode("Página $pageNumber de $totalPages"), 0, 0, 'R');
    }

    public function CellUtf8($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $txt = utf8_decode($this->limpiarTextoPDF($txt)); // conversión automática
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    public function MultiCellUtf8($w, $h, $txt, $border = 0, $align = 'L', $fill = false)
    {
        $txt = utf8_decode($this->limpiarTextoPDF($txt));
        $this->MultiCell($w, $h, $txt, $border, $align, $fill);
    }

    function limpiarTextoPDF($texto)
    {
        $reemplazos = [
            // comillas curvas → normales
            '“' => '"',
            '”' => '"',
            '„' => '"',
            '‟' => '"',
            '‘' => "'",
            '’' => "'",
            '‚' => "'",

            // guiones largos → guion normal
            '–' => '-',
            '—' => '-',

            // puntos suspensivos unicode → 3 puntos
            '…' => '...',

            // espacios especiales → espacio normal
            "\u{00A0}" => ' ', // espacio no separable (&nbsp;)
            "\u{2007}" => ' ',
            "\u{202F}" => ' ',

            // otros caracteres raros
            "´" => "'",     // acento agudo suelto
        ];

        return strtr($texto, $reemplazos);
    }
}
