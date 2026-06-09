<?php

namespace App\Services;

use App\Models\EjecucionTrazabilidad;
use App\Models\EjecucionArchivo;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EjecucionArchivoService
{
    private $modulo = "";

    public function __construct(private  CargarArchivoService $cargarArchivoService) {}

    /**
     * Cargar archivos
     *
     * @param EjecucionTrazabilidad $ejecucion_trazabilidad
     * @param Array $files
     * @return void
     */
    public function cargarArchivos(EjecucionTrazabilidad $ejecucion_trazabilidad, array $files, array $eliminados = []): void
    {
        Log::debug($files);
        foreach ($files as $key => $file) {
            $archivo = $file["file"];
            if ($archivo instanceof UploadedFile) {
                $ejecucion_trazabilidad_archivo = EjecucionArchivo::create([
                    "ejecucion_trazabilidad_id" => $ejecucion_trazabilidad->id,
                    // "archivo" => "default.jpg"
                ]);
                $this->subirImagen($ejecucion_trazabilidad_archivo, $archivo, $key);
            }
        }

        if (isset($eliminados) && is_array($eliminados) && count($eliminados) > 0) {
            foreach ($eliminados as $e) {
                $ejecucion_trazabilidad_archivo = EjecucionArchivo::find($e);
                if ($ejecucion_trazabilidad_archivo && $ejecucion_trazabilidad_archivo->archivo) {
                    $this->eliminar($ejecucion_trazabilidad_archivo->archivo);
                }
                $ejecucion_trazabilidad_archivo->delete();
            }
        }
    }

    /**
     * Subir archivo
     *
     * @param EjecucionArchivo $ejecucion_trazabilidad_archivo
     * @param UploadedFile $archivo
     * @return void
     */
    public function subirImagen(EjecucionArchivo $ejecucion_trazabilidad_archivo, UploadedFile $archivo, $key = null): void
    {
        if ($ejecucion_trazabilidad_archivo->archivo && $ejecucion_trazabilidad_archivo->archivo != "default.jpg") {
            \File::delete(public_path("imgs/ejecucion_trazabilidads/" . $ejecucion_trazabilidad_archivo->archivo));
        }

        $nombre = ($key ? $key : '') . $ejecucion_trazabilidad_archivo->id . time();
        $ejecucion_trazabilidad_archivo->archivo = $this->cargarArchivoService->cargarArchivo($archivo, public_path("imgs/ejecucion_trazabilidads"), $nombre);
        $ejecucion_trazabilidad_archivo->save();
    }

    public function eliminar($nombre)
    {
        \File::delete(public_path("imgs/ejecucion_trazabilidads/" . $nombre));
    }
}
