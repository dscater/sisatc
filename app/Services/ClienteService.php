<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClienteService
{
    private $modulo = "CLIENTES";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService, private MovimientoInventarioService $movimiento_inventario_service, private UserService $user_service) {}

    public function listado(): Collection
    {
        $clientes = Cliente::select("clientes.*")
            ->with(["segmentacion_zona"]);
        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $clientes->where("segmentacion_zona_id", $segmentacion_zona?->id);
        }
        $clientes = $clientes->where("status", 1)->get();
        return $clientes;
    }
    /**
     * Lista de clientes paginado con filtros
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
        $clientes = Cliente::select("clientes.*")
            ->with(["segmentacion_zona"])
            ->where("status", 1);

        if (Auth::user()->tipo != 'ADMINISTRADOR') {
            $segmentacion_zona = $this->user_service->getSegmentacionZona(Auth::user()->id);
            $clientes->where("segmentacion_zona_id", $segmentacion_zona?->id);
        }

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $clientes->where("clientes.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $clientes->whereBetween("clientes.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $clientes->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $clientes->orderBy($value[0], $value[1]);
            }
        }


        $clientes = $clientes->paginate($length, ['*'], 'page', $page);
        return $clientes;
    }

    /**
     * Crear cliente
     *
     * @param array $datos
     * @return Cliente
     */
    public function crear(array $datos): Cliente
    {

        $fecha_actual = Carbon::now("America/La_Paz")->format("Y-m-d");

        // $segmentacion_zona = $this->user_service->getSegmentacionZona(Auth::user()->id);
        // if (!$segmentacion_zona) {
        //     throw ValidationException::withMessages(["error" => "El usuario no tiene una segmentación de zona asignada."]);
        // }

        $cliente = Cliente::create([
            "nombre" => $datos["nombre"],
            "fono" => $datos["fono"],
            "razon_social" => $datos["razon_social"],
            "nit_ci" => $datos["nit_ci"],
            "dir" => $datos["dir"],
            "latitud" => $datos["latitud"],
            "longitud" => $datos["longitud"],
            "user_id" => Auth::user()->id,
            "segmentacion_zona_id" => $datos["segmentacion_zona_id"],
            "fecha_registro" => $fecha_actual,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN CLIENTE", $cliente);

        return $cliente;
    }

    /**
     * Actualizar cliente
     *
     * @param array $datos
     * @param Cliente $cliente
     * @return Cliente
     */
    public function actualizar(array $datos, Cliente $cliente): Cliente
    {
        $old_cliente = clone $cliente;

        // $segmentacion_zona = $this->user_service->getSegmentacionZona(Auth::user()->id);
        // if (!$segmentacion_zona) {
        //     throw ValidationException::withMessages(["error" => "El usuario no tiene una segmentación de zona asignada."]);
        // }

        $cliente->update([
            "nombre" => $datos["nombre"],
            "fono" => $datos["fono"],
            "razon_social" => $datos["razon_social"],
            "nit_ci" => $datos["nit_ci"],
            "dir" => $datos["dir"],
            "latitud" => $datos["latitud"],
            "longitud" => $datos["longitud"],
            "segmentacion_zona_id" => $datos["segmentacion_zona_id"],
            // "segmentacion_zona_id" => $segmentacion_zona?->id,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN CLIENTE", $old_cliente, $cliente->withoutRelations());

        return $cliente;
    }

    /**
     * Eliminar cliente
     *
     * @param Cliente $cliente
     * @return boolean
     */
    public function eliminar(Cliente $cliente): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_cliente = clone $cliente;
        $cliente->status = 0;
        $cliente->save();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN CLIENTE", $old_cliente, $cliente);

        return true;
    }
}
