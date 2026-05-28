<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaProductoStoreRequest;
use App\Http\Requests\CategoriaProductoUpdateRequest;
use App\Models\CategoriaProducto;
use App\Models\User;
use App\Services\CategoriaProductoService;
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

class CategoriaProductoController extends Controller
{
    public function __construct(private CategoriaProductoService $categoria_productoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/CategoriaProductos/Index");
    }

    /**
     * Listado de categoria_productos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "categoria_productos" => $this->categoria_productoService->listado()
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

        $categoria_productos = $this->categoria_productoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $categoria_productos->items(),
            "total" => $categoria_productos->total(),
            "lastPage" => $categoria_productos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo categoria_producto
     *
     * @param CategoriaProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(CategoriaProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el CategoriaProducto
            $this->categoria_productoService->crear($request->validated());
            DB::commit();
            return redirect()->route("categoria_productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un categoria_producto
     *
     * @param CategoriaProducto $categoria_producto
     * @return JsonResponse
     */
    public function show(CategoriaProducto $categoria_producto): JsonResponse
    {
        return response()->JSON($categoria_producto);
    }

    public function update(CategoriaProducto $categoria_producto, CategoriaProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar categoria_producto
            $this->categoria_productoService->actualizar($request->validated(), $categoria_producto);
            DB::commit();
            return redirect()->route("categoria_productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar categoria_producto
     *
     * @param CategoriaProducto $categoria_producto
     * @return JsonResponse|Response
     */
    public function destroy(CategoriaProducto $categoria_producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->categoria_productoService->eliminar($categoria_producto);
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
