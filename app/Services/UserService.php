<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    private $modulo = "USUARIOS";
    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}


    /**
     * Lista de users paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $users = User::select("users.*")
            ->where("users.id", "!=", 1);

        $users->buscarNombre($search);

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $users->orderBy($value[0], $value[1]);
            }
        }


        $users = $users->paginate($length, ['*'], 'page', $page);
        return $users;
    }


    /**
     * Lista de users paginado con filtros (eliminados)
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginadoEliminados(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $users = User::select("users.*")
            ->where("id", "!=", 1);
        $users->where("tipo", "!=", "POSTULANTE");
        $users->where("status", 0);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $users->where("users.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $users->whereBetween("users.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $users->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("users.$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $users->orderBy($value[0], $value[1]);
            }
        }


        $users = $users->paginate($length, ['*'], 'page', $page);
        return $users;
    }

    /**
     * Obtener nombre de usuario
     *
     * @param string $nom
     * @param string $apep
     * @return string
     */
    public function getNombreUsuario(string $nom, string $apep, int $id = 0): string
    {
        //determinando el nombre de usuario inicial del 1er_nombre+apep+tipoUser
        $cont = 0;
        do {
            $nombre = mb_strtoupper($nom);
            $paterno = mb_strtoupper($apep);
            $nombre_user = substr($nombre, 0, 1); //inicial 1er_nombre
            $nombre_user .= $paterno;
            if ($cont > 0) {
                $nombre_user = $nombre_user . $cont;
            }
            $cont++;

            $existe = User::where('usuario', $nombre_user)->get()->first();
            if ($id > 0 && $existe) {
                if ($existe->id == $id) {
                    $existe = false;
                }
            }
        } while ($existe);
        return $nombre_user;
    }
    /**
     * Crear user
     *
     * @param array $datos
     * @return User
     */
    public function crear(array $datos): User
    {
        $user = User::create([
            "usuario" => $datos["email"],
            "nombre" => mb_strtoupper($datos["nombre"]),
            "apellido" => mb_strtoupper($datos["apellido"]),
            "email" => $datos["email"],
            "fono" => $datos["fono"],
            "acceso" => $datos["acceso"],
            "password" => $datos["password"],
            "tipo" => $datos["tipo"],
            "fecha_registro" => date("Y-m-d")
        ]);

        // cargar foto
        if (isset($datos["foto"]) && !is_string($datos["foto"])) {
            $this->cargarFoto($user, $datos["foto"]);
        }
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN USUARIO", $user);
        return $user;
    }

    /**
     * Actualizar user
     *
     * @param array $datos
     * @param User $user
     * @return User
     */
    public function actualizar(array $datos, User $user): User
    {
        $old_user = clone $user;
        $user->update([
            "usuario" => $datos["email"],
            "nombre" => mb_strtoupper($datos["nombre"]),
            "apellido" => mb_strtoupper($datos["apellido"]),
            "email" => $datos["email"],
            "fono" => $datos["fono"],
            "acceso" => $datos["acceso"],
            "tipo" => $datos["tipo"],
        ]);

        if (!empty($datos["password"])) {
            $user->password = $datos["password"];
            $user->save();
        }

        // cargar foto
        if (isset($datos["foto"]) && !is_string($datos["foto"])) {
            $this->cargarFoto($user, $datos["foto"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN USUARIO", $old_user, $user->withoutRelations());
        return $user;
    }

    /**
     * Actualizar password
     *
     * @param array $datos
     * @param User $user
     * @return User
     */
    public function actualizarPassword(array $datos, User $user): User
    {
        $user->password = Hash::make($datos["password"]);
        $user->save();
        return $user;
    }

    /**
     * Cargar foto
     *
     * @param User $user
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(User $user, UploadedFile $foto): void
    {
        if ($user->foto) {
            \File::delete(public_path("imgs/users/" . $user->foto));
        }

        $nombre = $user->id . time();
        $user->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/users"), $nombre);
        $user->save();
    }

    /**
     * Eliminar user
     *
     * @param User $user
     * @return boolean
     */
    public function eliminar(User $user): bool
    {
        $old_user = clone $user;

        $usos = Certificado::where("user_id", $user->id)->get();
        if (count($usos) > 0) {
            throw new Exception("No es posible eliminar el registro porque esta ligado a otros registros");
        }
        $user->status = 0;
        $user->save();
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ AL USUARIO " . $old_user->usuario, $old_user, $user);

        return true;
    }

    /**
     * Reestablecer user
     *
     * @param User $user
     * @return boolean
     */
    public function reestablecer(User $user): bool
    {
        $old_user = clone $user;
        $user->status = 1;
        $user->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "REESTABLECER", "REESTABLECIÓ EL REGISTRO DE UN USUARIO " . $old_user->usuario, $old_user, $user);
        return true;
    }

    /**
     * Eliminación permanente de user
     *
     * @param User $user
     * @return boolean
     */
    public function eliminacion_permanente(User $user): bool
    {
        $old_user = clone $user;
        $user->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN PERMANENTE", "ELIMINÓ PERMANENTEMENTE EL REGISTRO DE UN USUARIO " . $old_user->usuario, $old_user);
        return true;
    }
}
