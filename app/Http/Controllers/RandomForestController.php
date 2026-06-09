<?php

namespace App\Http\Controllers;

use App\Models\Entrenamiento;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class RandomForestController extends Controller
{
    /**
     * Genera un dataset a partir de los registros históricos
     * almacenados en la tabla de entrenamiento.
     *
     * El dataset es exportado a formato JSON y enviado al
     * script de Python encargado de entrenar el modelo
     * Random Forest.
     *
     * Variables consideradas:
     * - Tipo de activo
     * - Módulo
     * - Tipo de falla
     * - Severidad
     * - Resultado
     * - Presencia de bug
     *
     * Objetivo:
     * Aprender patrones históricos para recomendar pruebas
     * en futuras ejecuciones de certificación.
     */
    public function entrenar()
    {
        $dataset = [];

        Entrenamiento::chunk(500, function ($registros) use (&$dataset) {
            foreach ($registros as $item) {

                $dataset[] = [
                    'tipo_activo' => $item->tipo_activo,
                    'modulo' => $item->modulo,
                    'tipo_falla' => $item->tipo_falla,
                    'severidad' => $item->severidad,
                    'prueba' => $item->prueba,
                    'resultado' => $item->resultado,
                    'bug' => $item->bug,
                ];
            }
        });

        $rutaDataset = public_path('scripts/dataset.json');
        file_put_contents(
            $rutaDataset,
            json_encode($dataset)
        );

        $script = public_path('scripts/script.py');
        $python = 'C:\\Users\\victo\\AppData\\Local\\Programs\\Python\\Python311\\python.exe';

        $process = new Process([
            $python,
            $script,
            'train',
            $rutaDataset
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception(
                $process->getErrorOutput()
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Modelo actualizado correctamente'
        ]);
    }

    /**
     * Solicita una predicción al modelo entrenado.
     *
     * Se envían las características principales del
     * escenario actual:
     *
     * - Tipo de activo
     * - Módulo
     * - Tipo de falla
     * - Severidad
     *
     * El modelo devuelve una lista de pruebas
     * recomendadas junto con su nivel de relevancia.
     */
    public function recomendar(Request $request)
    {
        $script = public_path('scripts/script.py');

        $payload = [
            'tipo_activo' => $request->tipo_activo,
            'modulo' => $request->modulo,
            'tipo_falla' => $request->tipo_falla,
            'severidad' => $request->severidad,
        ];
        $python = 'C:\\Users\\victo\\AppData\\Local\\Programs\\Python\\Python311\\python.exe';

        $process = new Process([
            $python,
            $script,
            'predict',
            json_encode($payload)
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception(
                $process->getErrorOutput()
            );
        }

        return response()->json(
            json_decode(
                $process->getOutput(),
                true
            )
        );
    }

    public function test_recomendar()
    {
        $script = public_path('scripts/script.py');

        $payload = [
            "tipo_activo" => "Api autenticacion",
            "modulo" => "Pagos",
            "tipo_falla" => "Timeout",
            "severidad" => "Critica",
        ];
        $python = 'C:\\Users\\victo\\AppData\\Local\\Programs\\Python\\Python311\\python.exe';

        $process = new Process([
            $python,
            $script,
            'predict',
            json_encode($payload)
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception(
                $process->getErrorOutput()
            );
        }

        return response()->json(
            json_decode(
                $process->getOutput(),
                true
            )
        );
    }
}
