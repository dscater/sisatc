<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivoPruebaStoreRequest;
use App\Http\Requests\ActivoPruebaUpdateRequest;
use App\Models\ActivoPrueba;
use App\Models\User;
use App\Services\ActivoPruebaService;
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

class ActivoPruebaController extends Controller
{
    public function __construct(private ActivoPruebaService $activo_pruebaService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/ActivoPruebas/Index");
    }

    /**
     * Listado de activo_pruebas sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "activo_pruebas" => $this->activo_pruebaService->listado($request->input("activo_id", ""))
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

        $activo_pruebas = $this->activo_pruebaService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $activo_pruebas->items(),
            "total" => $activo_pruebas->total(),
            "lastPage" => $activo_pruebas->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo activo_prueba
     *
     * @param ActivoPruebaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ActivoPruebaStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // crear el ActivoPrueba
            $this->activo_pruebaService->crear($request->validated());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro realizado',
            ]);
            // return redirect()->route("activo_pruebas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un activo_prueba
     *
     * @param ActivoPrueba $activo_prueba
     * @return JsonResponse
     */
    public function show(ActivoPrueba $activo_prueba): JsonResponse
    {
        return response()->JSON($activo_prueba);
    }

    public function update(ActivoPrueba $activo_prueba, ActivoPruebaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar activo_prueba
            $this->activo_pruebaService->actualizar($request->validated(), $activo_prueba);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro realizado',
            ]);
            // return redirect()->route("activo_pruebas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar activo_prueba
     *
     * @param ActivoPrueba $activo_prueba
     * @return JsonResponse|Response
     */
    public function destroy(ActivoPrueba $activo_prueba): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->activo_pruebaService->eliminar($activo_prueba);
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
