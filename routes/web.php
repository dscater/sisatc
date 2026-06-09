<?php

use App\Http\Controllers\ActivoController;
use App\Http\Controllers\ActivoPruebaController;
use App\Http\Controllers\AsignacionZonaController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComisionController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ConsolidadoController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\DistribucionController;
use App\Http\Controllers\EjecucionTrazabilidadController;
use App\Http\Controllers\EntrenamientoController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PresentacionController;
use App\Http\Controllers\PresentacionProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\RandomForestController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\SegmentacionZonaController;
use App\Http\Controllers\TipoActivoController;
use App\Http\Controllers\TipoUsuarioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('inicio');
    }
    return Inertia::render('Auth/Login');
});

Route::get('/entrenar', [RandomForestController::class, 'entrenar']);
Route::get('/test_recomendar', [RandomForestController::class, 'test_recomendar']);

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('inicio');
    }
    return Inertia::render('Auth/Login');
})->name("login");

Route::get("configuracions/getConfiguracion", [ConfiguracionController::class, 'getConfiguracion'])->name("configuracions.getConfiguracion");

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('optimize');
    return 'Cache eliminado <a href="/">Ir al inicio</a>';
})->name('clear.cache');

Route::get("sincronizarInicio", [CertificadoEmitidoController::class, 'sincronizarInicio']);

// ADMINISTRACION
Route::middleware(['auth', 'permisoUsuario'])->prefix("admin")->group(function () {
    // INICIO
    Route::get('/inicio', [InicioController::class, 'inicio'])->name('inicio');
    Route::get('/kpi', [InicioController::class, 'kpi'])->name('kpi');

    // CONFIGURACION
    Route::resource("configuracions", ConfiguracionController::class)->only(
        ["index", "show", "update"]
    );

    // USUARIO
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/update_foto', [ProfileController::class, 'update_foto'])->name('profile.update_foto');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get("getUser", [UserController::class, 'getUser'])->name('users.getUser');
    Route::get("permisosUsuario", [UserController::class, 'permisosUsuario']);

    // USUARIOS
    Route::put("usuarios/password/{user}", [UsuarioController::class, 'actualizaPassword'])->name("usuarios.password");
    Route::get("usuarios/paginado", [UsuarioController::class, 'paginado'])->name("usuarios.paginado");
    Route::get("usuarios/listado", [UsuarioController::class, 'listado'])->name("usuarios.listado");
    Route::get("usuarios/listadoAsignacions", [UsuarioController::class, 'listadoAsignacions'])->name("usuarios.listadoAsignacions");
    Route::get("usuarios/listado/byTipo", [UsuarioController::class, 'byTipo'])->name("usuarios.byTipo");
    Route::get("usuarios/segmentacion_zonas_asignadas", [UsuarioController::class, 'segmentacion_zonas_asignadas'])->name("usuarios.segmentacion_zonas_asignadas");
    Route::get("usuarios/show/{user}", [UsuarioController::class, 'show'])->name("usuarios.show");
    Route::put("usuarios/update/{user}", [UsuarioController::class, 'update'])->name("usuarios.update");
    Route::delete("usuarios/{user}", [UsuarioController::class, 'destroy'])->name("usuarios.destroy");
    Route::resource("usuarios", UsuarioController::class)->only(
        ["index", "store"]
    );

    // TIPO USUARIOS
    Route::get("tipo_usuarios/listado", [TipoUsuarioController::class, 'listado'])->name("tipo_usuarios.listado");

    // TIPO ACTIVOS
    Route::get("tipo_activos/paginado", [TipoActivoController::class, 'paginado'])->name("tipo_activos.paginado");
    Route::get("tipo_activos/listado", [TipoActivoController::class, 'listado'])->name("tipo_activos.listado");
    Route::get("tipo_activos/recomendacion", [TipoActivoController::class, 'recomendacion'])->name("tipo_activos.recomendacion");
    Route::get("tipo_activos/usuarios", [TipoActivoController::class, 'usuarios'])->name("tipo_activos.usuarios");
    Route::resource("tipo_activos", TipoActivoController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // ACTIVOS
    Route::get("activos/paginado", [ActivoController::class, 'paginado'])->name("activos.paginado");
    Route::get("activos/listado", [ActivoController::class, 'listado'])->name("activos.listado");
    Route::resource("activos", ActivoController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // ACTIVO PRUEBAS
    Route::get("activo_pruebas/paginado", [ActivoPruebaController::class, 'paginado'])->name("activo_pruebas.paginado");
    Route::get("activo_pruebas/listado", [ActivoPruebaController::class, 'listado'])->name("activo_pruebas.listado");
    Route::resource("activo_pruebas", ActivoPruebaController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // ENTRENAMIENTOS
    Route::get("entrenamientos/paginado", [EntrenamientoController::class, 'paginado'])->name("entrenamientos.paginado");
    Route::get("entrenamientos/listado", [EntrenamientoController::class, 'listado'])->name("entrenamientos.listado");
    Route::post("entrenamientos/registrarEntrenamiento", [EntrenamientoController::class, 'registrarEntrenamiento'])->name("entrenamientos.registrarEntrenamiento");
    Route::resource("entrenamientos", EntrenamientoController::class)->only(
        ["index", "create", "store", "edit", "show", "update", "destroy"]
    );

    // INCIDENCIAS
    Route::get("incidencias/paginado", [IncidenciaController::class, 'paginado'])->name("incidencias.paginado");
    Route::get("incidencias/listado", [IncidenciaController::class, 'listado'])->name("incidencias.listado");
    Route::resource("incidencias", IncidenciaController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // EJECUCION TRAZABILIDAD
    Route::get("ejecucion_trazabilidads/paginado", [EjecucionTrazabilidadController::class, 'paginado'])->name("ejecucion_trazabilidads.paginado");
    Route::get("ejecucion_trazabilidads/listado", [EjecucionTrazabilidadController::class, 'listado'])->name("ejecucion_trazabilidads.listado");
    Route::resource("ejecucion_trazabilidads", EjecucionTrazabilidadController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // REPORTES
    Route::get('reportes/usuarios', [ReporteController::class, 'usuarios'])->name("reportes.usuarios");
    Route::get('reportes/r_usuarios', [ReporteController::class, 'r_usuarios'])->name("reportes.r_usuarios");

    Route::get('reportes/log_users', [ReporteController::class, 'log_users'])->name("reportes.log_users");
    Route::get('reportes/r_log_users', [ReporteController::class, 'r_log_users'])->name("reportes.r_log_users");

    Route::get('reportes/certificacion', [ReporteController::class, 'certificacion'])->name("reportes.certificacion");
    Route::get('reportes/r_certificacion', [ReporteController::class, 'r_certificacion'])->name("reportes.r_certificacion");
});
require __DIR__ . '/auth.php';
