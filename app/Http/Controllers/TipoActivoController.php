<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoActivoStoreRequest;
use App\Http\Requests\TipoActivoUpdateRequest;
use App\Models\TipoActivo;
use App\Models\User;
use App\Services\TipoActivoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;

class TipoActivoController extends Controller
{
    public function __construct(private TipoActivoService $tipo_activoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/TipoActivos/Index");
    }

    /**
     * Listado de tipo_activos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "tipo_activos" => $this->tipo_activoService->listado()
        ]);
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

        $columnsSerachLike = [
            "nombre",
            "descripcion",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $tipo_activos = $this->tipo_activoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $tipo_activos->items(),
            "total" => $tipo_activos->total(),
            "lastPage" => $tipo_activos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo tipo_activo
     *
     * @param TipoActivoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(TipoActivoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el TipoActivo
            $this->tipo_activoService->crear($request->validated());
            DB::commit();
            return redirect()->route("tipo_activos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un tipo_activo
     *
     * @param TipoActivo $tipo_activo
     * @return JsonResponse
     */
    public function show(TipoActivo $tipo_activo): JsonResponse
    {
        return response()->JSON($tipo_activo);
    }

    public function update(TipoActivo $tipo_activo, TipoActivoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar tipo_activo
            $this->tipo_activoService->actualizar($request->validated(), $tipo_activo);
            DB::commit();
            return redirect()->route("tipo_activos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar tipo_activo
     *
     * @param TipoActivo $tipo_activo
     * @return JsonResponse|Response
     */
    public function destroy(TipoActivo $tipo_activo): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->tipo_activoService->eliminar($tipo_activo);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
