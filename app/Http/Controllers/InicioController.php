<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\CertificadoDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class InicioController extends Controller
{

    public function verificaLogin()
    {
        $sw = false;
        if (Auth::check()) {
            $sw = true;
        }

        return response()->JSON(["sw" => $sw]);
    }

    public function inicio()
    {
        $array_infos = app(UserController::class)->getInfoBoxUser();

        return Inertia::render('Admin/Home', compact('array_infos'));
    }

    public function login()
    {
        return Inertia::render("Auth/Login");
    }

    public function certificadosEmitidosLinea(Request $request)
    {
        $tipo =  $request->tipo;

        $recorrido = [];

        if ($tipo == 'semanal') {
            $fecha = Carbon::now("America/La_Paz");

            for ($i = 6; $i >= 0; $i--) {
                $recorrido[] = $fecha->copy()->subDays($i)->format("Y-m-d");
            }
        }
        if ($tipo == 'meses') {
            $fecha = Carbon::now("America/La_Paz");
            $mes_actual = $fecha->month;

            for ($i = 1; $i <= $mes_actual; $i++) {
                $recorrido[] = Carbon::create(null, $i, 1)->format("m");
            }
        }
        if ($tipo == 'gestion') {
            $recorrido = Certificado::selectRaw("YEAR(fecha_registro) as gestion")
                ->where("status", 1)
                ->groupBy("gestion")
                ->orderBy("gestion")
                ->pluck("gestion")
                ->toArray();
        }

        $data = [];

        $categories = [];
        $array_meses = [
            "01" => "Enero",
            "02" => "Febrero",
            "03" => "Marzo",
            "04" => "Abril",
            "05" => "Mayo",
            "06" => "Junio",
            "07" => "Julio",
            "08" => "Agosto",
            "09" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre",
        ];
        $total_final = 0;
        foreach ($recorrido as $item) {
            if ($tipo == 'semanal') {
                $total = CertificadoDetalle::whereHas("certificado", function ($q) use ($item) {
                    $q->whereDate('fecha_registro', $item);
                    $q->where("status", 1);
                    // $q->where("estado", 1);
                });

                $total->where("estado", 1);
                if (Auth::user()->tipo == 'MÉDICO') {
                    $total->where("user_id", Auth::user()->id);
                }
                $total = $total->count();
                $categories[] = date("d/m/Y", strtotime($item));
            }

            if ($tipo == 'meses') {
                $total = CertificadoDetalle::whereHas("certificado", function ($q) use ($item) {
                    $q->whereMonth('fecha_registro', $item);
                    $q->whereYear('fecha_registro', Carbon::now()->year);
                    $q->where("status", 1);
                    // $q->where("estado", 1);
                });
                $total->where("estado", 1);
                if (Auth::user()->tipo == 'MÉDICO') {
                    $total->where("user_id", Auth::user()->id);
                }

                $total = $total->count();
                $categories[] = $array_meses[$item];
            }

            if ($tipo == 'gestion') {
                $total = CertificadoDetalle::whereHas("certificado", function ($q) use ($item) {
                    $q->whereYear('fecha_registro', $item);
                    $q->where("status", 1);
                    // $q->where("estado", 1);
                });
                $total->where("estado", 1);
                if (Auth::user()->tipo == 'MÉDICO') {
                    $total->where("user_id", Auth::user()->id);
                }
                $total = $total->count();
                $categories[] = $item;
            }

            $total_final += (float)$total;
            $data[] = (float)$total;
        }

        return response()->JSON([
            "categories" => $categories,
            "data" => $data,
            "total_final" => $total_final
        ]);
    }

    public function cantidadTramitesNormal()
    {
        $normales = CertificadoDetalle::whereHas("certificado", function ($q) {
            $q->where("tipo", "NORMAL");
            $q->where("status", 1);
        });

        if (Auth::user()->tipo == 'MÉDICO') {
            $normales->where("user_id", Auth::user()->id);
        }
        $normales = $normales->count();

        $tramites = CertificadoDetalle::whereHas("certificado", function ($q) {
            $q->where("tipo", "TRAMITE");
            $q->where("status", 1);
        });

        if (Auth::user()->tipo == 'MÉDICO') {
            $tramites->where("user_id", Auth::user()->id);
        }
        $tramites = $tramites->count();

        $data = [
            ["name" => "TRÁMITE", "y" => (float)$tramites],
            ["name" => "NORMAL", "y" => (float)$normales],
        ];

        $total_final = (float)$normales + (float)$tramites;

        return response()->JSON([
            "data" => $data,
            "total_final" => $total_final
        ]);
    }
}
