<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntrenamientoStoreRequest;
use App\Http\Requests\EntrenamientoUpdateRequest;
use App\Models\Entrenamiento;
use App\Models\User;
use App\Services\EntrenamientoService;
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

class EntrenamientoController extends Controller
{
    public function __construct(private EntrenamientoService $entrenamientoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Entrenamientos/Index");
    }

    /**
     * Listado de entrenamientos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "entrenamientos" => $this->entrenamientoService->listado($request->input("activo_id", ""))
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

        $entrenamientos = $this->entrenamientoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $entrenamientos->items(),
            "total" => $entrenamientos->total(),
            "lastPage" => $entrenamientos->lastPage()
        ]);
    }


    public function create(): ResponseInertia
    {
        return Inertia::render("Admin/Entrenamientos/Create");
    }

    /**
     * Registrar un nuevo entrenamiento
     *
     * @param EntrenamientoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(EntrenamientoStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            // crear el Entrenamiento
            $this->entrenamientoService->crear($request->validated());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro realizado',
            ]);
            // return redirect()->route("entrenamientos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un entrenamiento
     *
     * @param Entrenamiento $entrenamiento
     * @return JsonResponse
     */
    public function show(Entrenamiento $entrenamiento): JsonResponse
    {
        return response()->JSON($entrenamiento);
    }

    public function update(Entrenamiento $entrenamiento, EntrenamientoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar entrenamiento
            $this->entrenamientoService->actualizar($request->validated(), $entrenamiento);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro realizado',
            ]);
            // return redirect()->route("entrenamientos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar entrenamiento
     *
     * @param Entrenamiento $entrenamiento
     * @return JsonResponse|Response
     */
    public function destroy(Entrenamiento $entrenamiento): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->entrenamientoService->eliminar($entrenamiento);
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
