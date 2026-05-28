<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfiguracionRequest;
use App\Models\Configuracion;
use App\Services\ConfiguracionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
    public function __construct(private ConfiguracionService $configuracionService) {}

    public function index(Request $request)
    {
        $configuracion = Configuracion::first();
        return Inertia::render("Admin/Configuracions/Index", compact("configuracion"));
    }

    public function getConfiguracion()
    {
        $configuracion = Configuracion::first()->setAppends(["url_logo"]);
        return response()->JSON([
            "configuracion" => $configuracion
        ], 200);
    }

    public function update(Configuracion $configuracion, ConfiguracionRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->configuracionService->actualizar($request->validated(), $configuracion);
            DB::commit();
            return redirect()->route("configuracions.index")->with("success", "Registro correcto");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(Configuracion $configuracion) {}
}
