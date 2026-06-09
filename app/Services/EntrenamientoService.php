<?php

namespace App\Services;

use App\Models\Activo;
use App\Services\HistorialAccionService;
use App\Models\Entrenamiento;
use App\Models\TipoActivo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EntrenamientoService
{
    private $modulo = "ENTRENAMIENTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado($activo_id): Collection
    {
        $entrenamientos = Entrenamiento::select("entrenamientos.*");

        if ($activo_id) {
            $entrenamientos->where("activo_id", $activo_id);
        }

        $entrenamientos = $entrenamientos->get();
        return $entrenamientos;
    }
    /**
     * Lista de entrenamientos paginado con filtros
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
        $entrenamientos = Entrenamiento::select("entrenamientos.*")
            ->with(["tipo_entrenamiento"]);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $entrenamientos->where("entrenamientos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $entrenamientos->whereBetween("entrenamientos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $entrenamientos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $entrenamientos->orderBy($value[0], $value[1]);
            }
        }


        $entrenamientos = $entrenamientos->paginate($length, ['*'], 'page', $page);
        return $entrenamientos;
    }

    /**
     * Crear entrenamiento
     *
     * @param array $datos
     * @return Entrenamiento
     */
    public function crear(array $datos)
    {
        $archivo = $datos['archivo'];

        $spreadsheet = IOFactory::load(
            $archivo->getRealPath()
        );

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray();

        $headers = array_map(function ($header) {
            $header = trim($header);
            $header = mb_strtolower($header);

            return str_replace(
                ['á', 'é', 'í', 'ó', 'ú'],
                ['a', 'e', 'i', 'o', 'u'],
                $header
            );
        }, $rows[0]);

        $columnas = array_flip($headers);
        Log::debug($columnas);
        $requeridas = [
            'tipo de activo',
            'modulo',
            'tipo de falla',
            'severidad',
            'prueba ejecutada',
            'resultado',
            'bug'
        ];

        foreach ($requeridas as $columna) {
            if (!isset($columnas[$columna])) {
                throw new \Exception("La columna '{$columna}' no existe en el archivo.");
            }
        }

        foreach (array_slice($rows, 1) as $fila) {
            Log::debug("ASDASD");

            $tipoActivo = $fila[$columnas['tipo de activo']] ?? null;

            // Buscar el activo en tu catálogo
            $tipo_activo = TipoActivo::where('nombre', $tipoActivo)->first();

            if (!$tipo_activo) {
                $tipo_activo = TipoActivo::create(["nombre" => $tipoActivo]);
            }

            $entrenamiento =  Entrenamiento::create([
                'tipo_activo'      => $tipoActivo,
                'tipo_activo_id'   => $tipo_activo?->id,
                'modulo'     => $fila[$columnas['modulo']] ?? null,
                'tipo_falla' => $fila[$columnas['tipo de falla']] ?? null,
                'severidad'  => $fila[$columnas['severidad']] ?? null,
                'prueba'     => $fila[$columnas['prueba ejecutada']] ?? null,
                'resultado'  => $fila[$columnas['resultado']] ?? null,
                'bug'        => $fila[$columnas['bug']] ?? null,

                // Campos del sistema
                'estado'      => 1,
                'res'         => 'IMPORTADO',
                'user_id'     => auth()->id(),
                'fecha'       => now()->toDateString(),
                'hora'        => now()->format('H:i:s'),
            ]);

            // registrar accion
            sleep(3);

            $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN GUIÓN DE PRUEBA", $entrenamiento);
        }

        return true;
    }

    /**
     * Actualizar entrenamiento
     *
     * @param array $datos
     * @param Entrenamiento $entrenamiento
     * @return Entrenamiento
     */
    public function actualizar(array $datos, Entrenamiento $entrenamiento): Entrenamiento
    {
        $old_entrenamiento = clone $entrenamiento;

        $entrenamiento->update([
            "activo_id" => $datos["activo_id"],
            "descripcion" => mb_strtoupper($datos["descripcion"]),
            "modulo" => mb_strtoupper($datos["modulo"]),
            "prueba" => mb_strtoupper($datos["prueba"]),
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN GUIÓN DE PRUEBA", $old_entrenamiento, $entrenamiento->withoutRelations());

        return $entrenamiento;
    }

    /**
     * Eliminar entrenamiento
     *
     * @param Entrenamiento $entrenamiento
     * @return boolean
     */
    public function eliminar(Entrenamiento $entrenamiento): bool|Exception
    {
        // TODO: VERIFICAR RELACIONES

        $old_entrenamiento = clone $entrenamiento;
        $entrenamiento->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN GUIÓN DE PRUEBA", $old_entrenamiento, $entrenamiento);

        return true;
    }
}
