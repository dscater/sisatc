<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncidenciaStoreRequest;
use App\Http\Requests\IncidenciaUpdateRequest;
use App\Models\Incidencia;
use App\Models\User;
use App\Services\IncidenciaService;
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

class IncidenciaController extends Controller
{
    public function __construct(private IncidenciaService $incidenciaService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Incidencias/Index");
    }

    /**
     * Listado de incidencias sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "incidencias" => $this->incidenciaService->listado()
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

        $incidencias = $this->incidenciaService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $incidencias->items(),
            "total" => $incidencias->total(),
            "lastPage" => $incidencias->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo incidencia
     *
     * @param IncidenciaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(IncidenciaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Incidencia
            $this->incidenciaService->crear($request->validated());
            DB::commit();
            return redirect()->route("incidencias.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un incidencia
     *
     * @param Incidencia $incidencia
     * @return JsonResponse
     */
    public function show(Incidencia $incidencia): JsonResponse
    {
        return response()->JSON($incidencia);
    }

    public function update(Incidencia $incidencia, IncidenciaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar incidencia
            $this->incidenciaService->actualizar($request->validated(), $incidencia);
            DB::commit();
            return redirect()->route("incidencias.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar incidencia
     *
     * @param Incidencia $incidencia
     * @return JsonResponse|Response
     */
    public function destroy(Incidencia $incidencia): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->incidenciaService->eliminar($incidencia);
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
