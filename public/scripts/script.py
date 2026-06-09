import os

import sys
import json
import pickle
import pandas as pd

from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder

# Ruta donde se almacena el modelo entrenado.
# El archivo contiene:
# - Modelo Random Forest
# - Codificadores utilizados para transformar texto a números
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

MODEL_FILE = os.path.join(
    BASE_DIR,
    "model.pkl"
)

def train(dataset_file):

    """
    Entrena el modelo utilizando los registros históricos.
    1. Leer dataset generado desde Laravel.
    2. Transformar variables categóricas a valores numéricos.
    3. Construir variables de entrada (X).
    4. Definir la prueba ejecutada como variable objetivo (y).
    5. Entrenar Random Forest.
    6. Persistir el modelo para futuras predicciones.
    """

    with open(dataset_file, "r", encoding="utf8") as f:
        registros = json.load(f)

    df = pd.DataFrame(registros)

    encoders = {}

    columnas = [
        "tipo_activo",
        "modulo",
        "tipo_falla",
        "severidad",
        "prueba",
        "resultado",
        "bug"
    ]

    # Conversión de texto a representación numérica.
    # Los algoritmos de Machine Learning requieren
    # trabajar con variables numéricas.

    for col in columnas:

        encoder = LabelEncoder()

        df[col] = encoder.fit_transform(
            df[col].astype(str)
        )

        encoders[col] = encoder

    # Variables utilizadas para identificar patrones.

    X = df[
        [
            "tipo_activo",
            "modulo",
            "tipo_falla",
            "severidad"
        ]
    ]

    # prueba que históricamente fue ejecutada.

    y = df["prueba"]

    # Configuración del modelo Random Forest.
    # n_estimators: cantidad de árboles de decisión.
    # max_depth_ profundidad máxima de cada árbol.

    model = RandomForestClassifier(
        n_estimators=200,
        max_depth=10,
        random_state=42
    )

    # Proceso de entrenamiento.

    model.fit(X, y)

    # Persistencia del modelo para reutilización.

    with open(MODEL_FILE, "wb") as f:

        pickle.dump(
            {
                "model": model,
                "encoders": encoders
            },
            f
        )

    print(
        json.dumps(
            {
                "success": True
            }
        )
    )


def predict(payload):

    """
    Genera recomendaciones utilizando el modelo
    previamente entrenado.
    1. Cargar modelo almacenado.
    2. Transformar datos de entrada.
    3. Ejecutar predicción.
    4. Obtener probabilidades asociadas a cada prueba.
    5. Ordenar resultados por relevancia.
    6. Retornar ranking de recomendaciones.
    """

    with open(MODEL_FILE, "rb") as f:
        bundle = pickle.load(f)

    model = bundle["model"]
    encoders = bundle["encoders"]

    data = json.loads(payload)

    # Transformación de variables de entrada.

    fila = [[
        encoders["tipo_activo"]
            .transform([str(data["tipo_activo"])])[0],

        encoders["modulo"]
            .transform([str(data["modulo"])])[0],

        encoders["tipo_falla"]
            .transform([str(data["tipo_falla"])])[0],

        encoders["severidad"]
            .transform([str(data["severidad"])])[0]
    ]]

    # Obtención de probabilidades para cada clase.

    probabilidades = model.predict_proba(fila)[0]

    # Recuperación de nombres originales de pruebas.

    clases = encoders["prueba"].inverse_transform(
        range(len(probabilidades))
    )

    resultados = []

    for prueba, probabilidad in zip(
        clases,
        probabilidades
    ):

        resultados.append({
            "prueba": prueba,
            "porcentaje": round(
                float(probabilidad) * 100,
                2
            )
        })

    # Ordenamiento descendente por relevancia.

    resultados.sort(
        key=lambda x: x["porcentaje"],
        reverse=True
    )

    print(
        json.dumps(
            {
                "algoritmo": "Random Forest",
                "recomendaciones": resultados[:5]
            }
        )
    )


if __name__ == "__main__":

    if len(sys.argv) < 2:
        print(
            json.dumps({
                "success": False,
                "message": "Debe especificar una acción"
            })
        )
        sys.exit()

    action = sys.argv[1]

    if action == "train":

        if len(sys.argv) < 3:
            print(
                json.dumps({
                    "success": False,
                    "message": "Debe proporcionar dataset"
                })
            )
            sys.exit()

        train(sys.argv[2])

    elif action == "predict":

        if len(sys.argv) < 3:
            print(
                json.dumps({
                    "success": False,
                    "message": "Debe proporcionar datos"
                })
            )
            sys.exit()

        predict(sys.argv[2])