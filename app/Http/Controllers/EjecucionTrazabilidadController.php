<?php

namespace App\Http\Controllers;

use App\Http\Requests\EjecucionTrazabilidadStoreRequest;
use App\Http\Requests\EjecucionTrazabilidadUpdateRequest;
use App\Models\EjecucionTrazabilidad;
use App\Models\User;
use App\Services\EjecucionTrazabilidadService;
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

class EjecucionTrazabilidadController extends Controller
{
    public function __construct(private EjecucionTrazabilidadService $ejecucion_trazabilidadService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/EjecucionTrazabilidads/Index");
    }

    /**
     * Listado de ejecucion_trazabilidads sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "ejecucion_trazabilidads" => $this->ejecucion_trazabilidadService->listado()
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

        $ejecucion_trazabilidads = $this->ejecucion_trazabilidadService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $ejecucion_trazabilidads->items(),
            "total" => $ejecucion_trazabilidads->total(),
            "lastPage" => $ejecucion_trazabilidads->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo ejecucion_trazabilidad
     *
     * @param EjecucionTrazabilidadStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(EjecucionTrazabilidadStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el EjecucionTrazabilidad
            $this->ejecucion_trazabilidadService->crear($request->validated());
            DB::commit();
            return redirect()->route("ejecucion_trazabilidads.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un ejecucion_trazabilidad
     *
     * @param EjecucionTrazabilidad $ejecucion_trazabilidad
     * @return JsonResponse
     */
    public function show(EjecucionTrazabilidad $ejecucion_trazabilidad): JsonResponse
    {
        return response()->JSON($ejecucion_trazabilidad);
    }

    public function update(EjecucionTrazabilidad $ejecucion_trazabilidad, EjecucionTrazabilidadUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar ejecucion_trazabilidad
            $this->ejecucion_trazabilidadService->actualizar($request->validated(), $ejecucion_trazabilidad);
            DB::commit();
            return redirect()->route("ejecucion_trazabilidads.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar ejecucion_trazabilidad
     *
     * @param EjecucionTrazabilidad $ejecucion_trazabilidad
     * @return JsonResponse|Response
     */
    public function destroy(EjecucionTrazabilidad $ejecucion_trazabilidad): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->ejecucion_trazabilidadService->eliminar($ejecucion_trazabilidad);
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
