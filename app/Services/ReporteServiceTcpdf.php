<?php

namespace App\Services;

use App\Models\Configuracion;
use TCPDF;

class ReporteServiceTcpdf extends TCPDF
{
    public function Header()
    {
        // if ($this->tocpage) or
        if ($this->page == 1) {
            $configuracion = Configuracion::first();
            $path_foto = public_path("imgs/" . $configuracion->logo);
            $extension = pathinfo($path_foto, PATHINFO_EXTENSION);
            if (file_exists($path_foto)) {
                $this->Image($path_foto, 10, 5, 25, '', $extension);
            }
            $this->Ln(7);

            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(0, 6, $configuracion->razon_social, 0, 1, 'C', 0, '', 0, false);
        } else {
            // *** replace the following parent::Header() with your code for other pages
            //parent::Header();
            // following will add your own logo ant text to other pages
            // $this->Image('http://localhost/other_pages_logo.png', 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            // $this->SetFont('helvetica', 'B', 14);
            // $this->Cell(0, 15, 'Other pages header text', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
    }

    function Footer()
    {
        $this->SetY(-8);
        $this->SetFont('helvetica', 'B', 10);

        $pageNumber = $this->getAliasNumPage();
        $totalPages = $this->getAliasNbPages();

        // usar ancho 0 = hasta el margen derecho
        $this->setCellPadding(0);
        $this->Cell(
            0, // ancho automático hasta margen derecho
            5,
            "Página $pageNumber de $totalPages",
            0,
            0,
            'R'
        );
    }
}
