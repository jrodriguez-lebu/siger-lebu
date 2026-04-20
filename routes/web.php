<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WhatsAppAdminController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════
// PORTAL PÚBLICO (sin autenticación)
// ═══════════════════════════════════════════════════════
Route::get('/', [PublicController::class, 'index'])->name('public.index');
Route::get('/reportar-emergencia', [PublicController::class, 'reportForm'])->name('public.report');
Route::post('/reportar-emergencia', [PublicController::class, 'reportStore'])->name('public.report.store')->middleware('throttle:5,1');
Route::get('/reportar-emergencia/gracias', [PublicController::class, 'thankYou'])->name('public.thank-you');
Route::get('/mapa', [PublicController::class, 'map'])->name('public.map');

// ═══════════════════════════════════════════════════════
// AUTENTICACIÓN
// ═══════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ═══════════════════════════════════════════════════════
// PORTAL PRIVADO (requiere autenticación)
// ═══════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Emergencias ──────────────────────────────────────────
    Route::prefix('emergencias')->name('emergencies.')->group(function () {
        Route::get('/', [EmergencyController::class, 'index'])->name('index');
        Route::get('/crear', [EmergencyController::class, 'create'])->name('create')->middleware('role:admin,coordinador,digitador');
        Route::post('/', [EmergencyController::class, 'store'])->name('store')->middleware('role:admin,coordinador,digitador');
        Route::get('/{emergency}', [EmergencyController::class, 'show'])->name('show');
        Route::get('/{emergency}/editar', [EmergencyController::class, 'edit'])->name('edit')->middleware('role:admin,coordinador');
        Route::put('/{emergency}', [EmergencyController::class, 'update'])->name('update')->middleware('role:admin,coordinador');
        Route::delete('/{emergency}', [EmergencyController::class, 'destroy'])->name('destroy')->middleware('role:admin');
        Route::post('/{emergency}/estado', [EmergencyController::class, 'changeStatus'])->name('changeStatus')->middleware('role:admin,coordinador,lider');
        Route::post('/{emergency}/asignar', [EmergencyController::class, 'assignTeam'])->name('assignTeam')->middleware('role:admin,coordinador');
        Route::post('/{emergency}/fotos', [EmergencyController::class, 'uploadPhotos'])->name('uploadPhotos');
        Route::delete('/{emergency}/fotos/{photo}', [EmergencyController::class, 'deletePhoto'])->name('deletePhoto');
        Route::get('/{emergency}/historial', [EmergencyController::class, 'history'])->name('history');
    });

    // ── Mapa Operativo ───────────────────────────────────────
    Route::get('/mapa-operativo', [MapController::class, 'index'])->name('map.index');
    Route::get('/api/emergencias-geojson', [MapController::class, 'geojson'])->name('map.geojson');
    Route::post('/api/emergencias/{emergency}/coordenadas', [MapController::class, 'updateCoordinates'])->name('map.updateCoords')->middleware('role:admin,coordinador');

    // ── Equipos ──────────────────────────────────────────────
    Route::prefix('equipos')->name('teams.')->middleware('role:admin,coordinador,lider')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/crear', [TeamController::class, 'create'])->name('create')->middleware('role:admin,coordinador');
        Route::post('/', [TeamController::class, 'store'])->name('store')->middleware('role:admin,coordinador');
        Route::get('/{team}', [TeamController::class, 'show'])->name('show');
        Route::get('/{team}/editar', [TeamController::class, 'edit'])->name('edit')->middleware('role:admin,coordinador');
        Route::put('/{team}', [TeamController::class, 'update'])->name('update')->middleware('role:admin,coordinador');
        Route::delete('/{team}', [TeamController::class, 'destroy'])->name('destroy')->middleware('role:admin');
        Route::post('/{team}/miembros', [TeamController::class, 'addMember'])->name('addMember')->middleware('role:admin,coordinador');
        Route::delete('/{team}/miembros/{personnel}', [TeamController::class, 'removeMember'])->name('removeMember')->middleware('role:admin,coordinador');
        Route::post('/{team}/vehiculos', [TeamController::class, 'addVehicle'])->name('addVehicle')->middleware('role:admin,coordinador');
        Route::delete('/{team}/vehiculos/{vehicle}', [TeamController::class, 'removeVehicle'])->name('removeVehicle')->middleware('role:admin,coordinador');
    });

    // ── Vehículos ────────────────────────────────────────────
    Route::resource('vehiculos', VehicleController::class)->middleware('role:admin,coordinador,lider')->parameters(['vehiculos' => 'vehicle'])->names([
        'index'   => 'vehicles.index',
        'create'  => 'vehicles.create',
        'store'   => 'vehicles.store',
        'show'    => 'vehicles.show',
        'edit'    => 'vehicles.edit',
        'update'  => 'vehicles.update',
        'destroy' => 'vehicles.destroy',
    ]);

    // ── Equipamiento ─────────────────────────────────────────
    Route::resource('equipamiento', EquipmentController::class)->middleware('role:admin,coordinador,lider')->parameters(['equipamiento' => 'equipment'])->names([
        'index'   => 'equipment.index',
        'create'  => 'equipment.create',
        'store'   => 'equipment.store',
        'show'    => 'equipment.show',
        'edit'    => 'equipment.edit',
        'update'  => 'equipment.update',
        'destroy' => 'equipment.destroy',
    ]);

    // ── Insumos ──────────────────────────────────────────────
    Route::resource('insumos', SupplyController::class)->middleware('role:admin,coordinador,lider')->parameters(['insumos' => 'supply'])->names([
        'index'   => 'supplies.index',
        'create'  => 'supplies.create',
        'store'   => 'supplies.store',
        'show'    => 'supplies.show',
        'edit'    => 'supplies.edit',
        'update'  => 'supplies.update',
        'destroy' => 'supplies.destroy',
    ]);

    // ── Personal ─────────────────────────────────────────────
    Route::resource('personal', PersonnelController::class)
        ->middleware('role:admin,coordinador,lider')
        ->parameters(['personal' => 'personnel'])
        ->names([
        'index'   => 'personnel.index',
        'create'  => 'personnel.create',
        'store'   => 'personnel.store',
        'show'    => 'personnel.show',
        'edit'    => 'personnel.edit',
        'update'  => 'personnel.update',
        'destroy' => 'personnel.destroy',
    ]);

    // ── Reportes ─────────────────────────────────────────────
    Route::prefix('reportes')->name('reports.')->middleware('role:admin,coordinador')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/emergencias', [ReportController::class, 'emergencies'])->name('emergencies');
        Route::get('/emergencias/pdf', [ReportController::class, 'exportPdf'])->name('exportPdf');
        Route::get('/emergencias/excel', [ReportController::class, 'exportExcel'])->name('exportExcel');
        Route::get('/inventario', [ReportController::class, 'inventory'])->name('inventory');
    });

    // ── WhatsApp logs (solo admin) ───────────────────────────
    Route::get('/whatsapp', [WhatsAppAdminController::class, 'index'])->name('whatsapp.index')->middleware('role:admin');

    // ── Usuarios (solo admin) ────────────────────────────────
    Route::prefix('usuarios')->name('users.')->middleware('role:admin')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/crear', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/editar', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle', [UserController::class, 'toggleActive'])->name('toggleActive');
    });

    // ── API interna para AJAX ────────────────────────────────
    Route::get('/api/vehiculos-disponibles', [VehicleController::class, 'available'])->name('api.vehicles.available');
    Route::get('/api/equipos-activos', [TeamController::class, 'active'])->name('api.teams.active');
    Route::get('/api/usuarios-lideres', [UserController::class, 'leaders'])->name('api.users.leaders');
});
