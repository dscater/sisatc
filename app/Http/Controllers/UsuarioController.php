<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\AsignacionZona;
use App\Models\HistorialAccion;
use App\Models\SegmentacionZona;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UsuarioController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(): InertiaResponse
    {
        return Inertia::render("Admin/Usuarios/Index");
    }

    public function clientes(): InertiaResponse
    {
        return Inertia::render("Admin/Usuarios/Clientes");
    }

    public function listado(Request $request): JsonResponse
    {
        $usuarios = User::where("id", "!=", 1);
        if (isset($request->tipo)) {
            if (is_array($request->tipo)) {
                $usuarios->whereIn("tipo", $request->tipo);
            } else {
                $usuarios->where("tipo", $request->tipo);
            }
        }
        $usuarios = $usuarios->where("status", 1)->get();
        return response()->JSON([
            "usuarios" => $usuarios
        ]);
    }

    public function listadoAsignacions(Request $request)
    {
        $usuarios = User::where("id", "!=", 1);

        if (isset($request->tipo)) {
            if (is_array($request->tipo)) {
                $usuarios->whereIn("tipo", $request->tipo);
            } else {
                $usuarios->where("tipo", $request->tipo);
            }
        }

        $usuarios = $usuarios->where("status", 1)
            ->get()
            ->map(function ($usuario) use ($request) {
                $zonaAsignada = $usuario->asignacion_zona;
                $usuario->segmentacion_zona_id = $zonaAsignada?->segmentacion_zona_id;
                $usuario->asignado = $usuario->asignacion_zona()
                    // ->where('segmentacion_zona_id', $request->zona_id)
                    ->exists();

                return $usuario;
            });

        return response()->json([
            "usuarios" => $usuarios
        ]);
    }

    public function byTipo(Request $request)
    {
        $usuarios = User::where("id", "!=", 1);
        if (isset($request->tipo) && trim($request->tipo) != "") {
            $usuarios = $usuarios->where("tipo", $request->tipo);
        }

        if ($request->order && $request->order == "desc") {
            $usuarios->orderBy("users.id", "desc");
        }

        $usuarios = $usuarios->where("status", 1)->get();

        return response()->JSON([
            "usuarios" => $usuarios
        ]);
    }
    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

        $columnsSerachLike = [];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $personas = $this->userService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $personas->items(),
            "total" => $personas->total(),
            "lastPage" => $personas->lastPage()
        ]);
    }

    /**
     * Store user
     *
     * @param UserStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(UserStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->userService->crear($request->validated());
            DB::commit();
            return redirect()->route("usuarios.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(User $user)
    {
        return response()->JSON($user->load("persona"));
    }

    public function actualizaAcceso(User $user, Request $request)
    {
        $user->acceso = $request->acceso;
        $user->save();
        return response()->JSON([
            "user" => $user,
            "message" => "Acceso actualizado"
        ]);
    }

    /**
     * Update user
     *
     * @param User $user
     * @param UserUpdateRequest $request
     * @return RedirectResponse|Response
     */
    public function update(User $user, UserUpdateRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->userService->actualizar($request->validated(), $user);
            DB::commit();
            return redirect()->route("usuarios.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function actualizaPassword(User $user, UserPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->userService->actualizarPassword($request->validated(), $user);
            DB::commit();

            if ($user->tipo == 'POSTULANTE') {
                return redirect()->route($request->redireccion ?? "postulantes.index")->with("bien", "Registro actualizado");
            }
            return redirect()->route("usuarios.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete user
     *
     * @param User $user
     * @return JsonResponse|Response
     */
    public function destroy(User $user): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->userService->eliminar($user);
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

    /**
     * Página eliminados
     *
     * @return Response
     */
    public function eliminados(): InertiaResponse
    {
        return Inertia::render("Admin/Usuarios/Eliminados");
    }

    /**
     * Listado Páginado de personas eliminados
     *
     * @param Request $request
     * @return void
     */
    public function paginado_eliminados(Request $request): JsonResponse
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderByCol = $request->orderByCol;
        $desc = $request->desc;

        $columnsSerachLike = ["nombre", "paterno", "materno", "ci", "fono", "dir"];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderByCol && $desc) {
            $arrayOrderBy = [
                [$orderByCol, $desc]
            ];
        }

        $personas = $this->userService->listadoPaginadoEliminados($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $personas->items(),
            "total" => $personas->total(),
            "lastPage" => $personas->lastPage()
        ]);
    }

    /**
     * Reestablecer una persona
     *
     * @param User $user
     * @return JsonResponse
     */
    public function reestablecer(User $user)
    {
        DB::beginTransaction();
        try {
            $this->userService->reestablecer($user);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se reestableció correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminación permanente de una user
     *
     * @param User $user
     * @return JsonResponse
     */
    public function eliminacion_permanente(User $user)
    {
        DB::beginTransaction();
        try {
            $this->userService->eliminacion_permanente($user);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó permanentemente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function segmentacion_zonas_asignadas()
    {
        $segmentacion_zonas = SegmentacionZona::whereHas("asignacion_zonas", function ($q) {
            if (Auth::user()->tipo != 'ADMINISTRADOR')
                $q->where("user_id", Auth::user()->id);
        })->get();
        return response()->JSON($segmentacion_zonas);
    }
}
