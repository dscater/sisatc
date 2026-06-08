<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivoStoreRequest;
use App\Http\Requests\ActivoUpdateRequest;
use App\Models\Activo;
use App\Models\User;
use App\Services\ActivoService;
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

class ActivoController extends Controller
{
    public function __construct(private ActivoService $activoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Activos/Index");
    }

    /**
     * Listado de activos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "activos" => $this->activoService->listado()
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

        $activos = $this->activoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $activos->items(),
            "total" => $activos->total(),
            "lastPage" => $activos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo activo
     *
     * @param ActivoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ActivoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Activo
            $this->activoService->crear($request->validated());
            DB::commit();
            return redirect()->route("activos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un activo
     *
     * @param Activo $activo
     * @return JsonResponse
     */
    public function show(Activo $activo): JsonResponse
    {
        return response()->JSON($activo);
    }

    public function update(Activo $activo, ActivoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar activo
            $this->activoService->actualizar($request->validated(), $activo);
            DB::commit();
            return redirect()->route("activos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar activo
     *
     * @param Activo $activo
     * @return JsonResponse|Response
     */
    public function destroy(Activo $activo): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->activoService->eliminar($activo);
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
