<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\Certificado;
use App\Models\CertificadoDetalle;
use App\Models\EjecucionTrazabilidad;
use App\Models\Incidencia;
use App\Models\TipoActivo;
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

    public function kpi(Request $request)
    {
        $activoId = $request->activo_id;
        $estado = $request->estado;
        $fechaInicio = $request->fecha_ini;
        $fechaFin = $request->fecha_fin;

        $queryCertificaciones = EjecucionTrazabilidad::query();
        $queryIncidencias = Incidencia::query();

        if ($estado && $estado != 'TODOS') {
            $queryCertificaciones->where('estado', $estado);
            $queryIncidencias->where('estado', $estado);
        }

        if ($activoId && $activoId != 'todos') {
            $queryCertificaciones->where('activo_id', $activoId);

            $activo = Activo::find($activoId);

            if ($activo) {
                $queryIncidencias->where(
                    'tipo_activo_id',
                    $activo->tipo_activo_id
                );
            }
        }

        if ($fechaInicio && $fechaFin) {
            $queryCertificaciones->whereBetween(
                'fecha',
                [$fechaInicio, $fechaFin]
            );

            $queryIncidencias->whereBetween(
                'fecha',
                [$fechaInicio, $fechaFin]
            );
        }

        $certificaciones = $queryCertificaciones->get();
        $incidencias = $queryIncidencias->get();

        $tipoActivos = TipoActivo::all();

        $detalle = [];

        foreach ($tipoActivos as $tipoActivo) {

            $certificacionesActivo = $certificaciones->filter(function ($item) use ($tipoActivo) {

                return optional($item->activo)->tipo_activo_id == $tipoActivo->id;
            });

            $incidenciasActivo = $incidencias->where(
                'tipo_activo_id',
                $tipoActivo->id
            );

            $totalCertificaciones = $certificacionesActivo->count();

            $totalIncidencias = $incidenciasActivo->count();

            $totalBugs = $incidenciasActivo
                ->where('bug', 'SI')
                ->count();

            $efectividad = $totalIncidencias > 0
                ? round(
                    ($totalBugs / $totalIncidencias) * 100,
                    2
                )
                : 0;

            $detalle[] = [
                'tipo_activo_id' => $tipoActivo->id,
                'activo' => $tipoActivo->nombre,
                'certificaciones' => $totalCertificaciones,
                'efectividad' => $efectividad,
            ];
        }

        return response()->json([
            'categorias' => collect($detalle)->pluck('activo'),
            'certificaciones' => collect($detalle)->pluck('certificaciones'),
            'efectividad' => collect($detalle)->pluck('efectividad'),
            'detalle' => $detalle
        ]);
    }
}
