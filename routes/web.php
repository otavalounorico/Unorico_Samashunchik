<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Models\User;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\PermissionManagerController;
use App\Http\Controllers\CantonController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\FallecidoController;
use App\Http\Controllers\BloqueController;
use App\Http\Controllers\NichoController;
use App\Http\Controllers\SocioNichoController;
use App\Http\Controllers\BeneficioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\BloqueGeomController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\AsignacionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
Route::get('/tables', function () {
    return view('tables');
})->name('tables')->middleware('auth');
Route::get('/wallet', function () {
    return view('wallet');
})->name('wallet')->middleware('auth');
Route::get('/RTL', function () {
    return view('RTL');
})->name('RTL')->middleware('auth');
Route::get('/profile', function () {
    return view('account-pages.profile');
})->name('profile')->middleware('auth');

Route::get('/signin', function () {
    return view('account-pages.signin');
})->name('signin');
Route::get('/signup', function () {
    return view('account-pages.signup');
})->name('signup')->middleware('guest');
Route::get('/sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('sign-up');
Route::post('/sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('/sign-in', [LoginController::class, 'create'])->middleware('guest')->name('sign-in');
Route::post('/sign-in', [LoginController::class, 'store'])->middleware('guest');

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'store'])->middleware('guest');

// Profile
Route::get('/laravel-examples/user-profile', [ProfileController::class, 'index'])->name('users.profile')->middleware('auth');
Route::put('/laravel-examples/user-profile/update', [ProfileController::class, 'update'])->name('users.profile.update')->middleware('auth');

// cambios para el CEMENTERIO

// Gestión de roles y permisos con Spatie/Permission

Route::middleware(['auth', 'role:Administrador'])->group(function () {

    // 1) Rutas estáticas del gestor global (primero)
    Route::get('/roles/permissions-manager', [PermissionManagerController::class, 'index'])
        ->name('roles.permissions.manager');
    Route::post('/roles/permissions-manager', [PermissionManagerController::class, 'update'])
        ->name('roles.permissions.manager.update');

    // 2) CRUD de roles (sin show)
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')->whereNumber('role');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')->whereNumber('role');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')->whereNumber('role');

    // 3) Asignación de permisos POR ROL (opcional si usas esta pantalla)
    Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'edit'])
        ->name('roles.permissions.edit')->whereNumber('role');
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'update'])
        ->name('roles.permissions.update')->whereNumber('role');
});

//Nuevo para todos los modulos
// Auditoría: solo ver auditoria
Route::middleware(['auth', 'permission:ver auditoria'])->get('/audits', [AuditController::class, 'index'])->name('auditoria.index');

// Aqui va lo de cantones, parroquias y comunidades
Route::middleware(['web', 'auth'])->group(function () {

    // =========================
    // Cantones
    // =========================

    Route::get('cantones', [CantonController::class, 'index'])->name('cantones.index');
    Route::get('cantones/create', [CantonController::class, 'create'])->name('cantones.create'); // Ahora esta llamará a la vista correctamente
    Route::post('cantones', [CantonController::class, 'store'])->name('cantones.store');
    Route::get('cantones/{canton}', [CantonController::class, 'show'])->name('cantones.show');
    Route::get('cantones/{canton}/edit', [CantonController::class, 'edit'])->name('cantones.edit'); // Esta también
    Route::put('cantones/{canton}', [CantonController::class, 'update'])->name('cantones.update');
    Route::delete('cantones/{canton}', [CantonController::class, 'destroy'])->name('cantones.destroy');
    // =========================
    // Parroquias
    // =========================
    Route::get('parroquias', [ParroquiaController::class, 'index'])->name('parroquias.index');
    Route::get('parroquias/create', [ParroquiaController::class, 'create'])->name('parroquias.create');
    Route::post('parroquias', [ParroquiaController::class, 'store'])->name('parroquias.store');
    Route::get('parroquias/{parroquia}', [ParroquiaController::class, 'show'])->name('parroquias.show');
    Route::get('parroquias/{parroquia}/edit', [ParroquiaController::class, 'edit'])->name('parroquias.edit');
    Route::put('parroquias/{parroquia}', [ParroquiaController::class, 'update'])->name('parroquias.update');
    Route::delete('parroquias/{parroquia}', [ParroquiaController::class, 'destroy'])->name('parroquias.destroy');
    Route::post('/parroquias/reportes', [ParroquiaController::class, 'generateReports'])->name('parroquias.reports');
    Route::get('cantones/{canton}/parroquias', [ParroquiaController::class, 'byCanton'])->name('cantones.parroquias');

    // =========================
    // Comunidades
    // =========================
    Route::get('comunidades', [ComunidadController::class, 'index'])->name('comunidades.index');
    Route::get('comunidades/create', [ComunidadController::class, 'create'])->name('comunidades.create');
    Route::post('comunidades', [ComunidadController::class, 'store'])->name('comunidades.store');
    Route::get('comunidades/{comunidad}', [ComunidadController::class, 'show'])->name('comunidades.show');
    Route::get('comunidades/{comunidad}/edit', [ComunidadController::class, 'edit'])->name('comunidades.edit');
    Route::put('comunidades/{comunidad}', [ComunidadController::class, 'update'])->name('comunidades.update');
    Route::delete('comunidades/{comunidad}', [ComunidadController::class, 'destroy'])->name('comunidades.destroy');
    Route::post('comunidades/reports', [App\Http\Controllers\ComunidadController::class, 'reports'])->name('comunidades.reports');

    //SOCIOS
    Route::get('/socios', [SocioController::class, 'index'])->name('socios.index');
    Route::get('/socios/create', [SocioController::class, 'create'])->name('socios.create');
    Route::post('/socios', [SocioController::class, 'store'])->name('socios.store');
    Route::get('/socios/{socio}', [SocioController::class, 'show'])->name('socios.show');
    Route::get('/socios/{socio}/edit', [SocioController::class, 'edit'])->name('socios.edit');
    Route::put('/socios/{socio}', [SocioController::class, 'update'])->name('socios.update');
    Route::patch('/socios/{socio}', [SocioController::class, 'update']); // opcional, PATCH y PUT son equivalentes
    Route::delete('/socios/{socio}', [SocioController::class, 'destroy'])->name('socios.destroy');
    Route::post('socios/reports', [SocioController::class, 'reports'])->name('socios.reports');

    //FALLECIDOS
    Route::get('/fallecidos', [FallecidoController::class, 'index'])->name('fallecidos.index');
    Route::get('/fallecidos/create', [FallecidoController::class, 'create'])->name('fallecidos.create');
    Route::post('/fallecidos', [FallecidoController::class, 'store'])->name('fallecidos.store');
    Route::get('/fallecidos/{fallecido}', [FallecidoController::class, 'show'])->name('fallecidos.show');
    Route::get('/fallecidos/{fallecido}/edit', [FallecidoController::class, 'edit'])->name('fallecidos.edit');
    Route::put('/fallecidos/{fallecido}', [FallecidoController::class, 'update'])->name('fallecidos.update');
    Route::patch('/fallecidos/{fallecido}', [FallecidoController::class, 'update']); // opcional
    Route::delete('/fallecidos/{fallecido}', [FallecidoController::class, 'destroy'])->name('fallecidos.destroy');
    Route::post('fallecidos/reports', [FallecidoController::class, 'reports'])->name('fallecidos.reports');

    //BLOQUES
    Route::get('/bloques', [BloqueController::class, 'index'])->name('bloques.index');
    Route::get('/bloques/create', [BloqueController::class, 'create'])->name('bloques.create');
    Route::post('/bloques', [BloqueController::class, 'store'])->name('bloques.store');
    Route::get('/bloques/{bloque}', [BloqueController::class, 'show'])->name('bloques.show');
    Route::get('/bloques/{bloque}/edit', [BloqueController::class, 'edit'])->name('bloques.edit');
    Route::put('/bloques/{bloque}', [BloqueController::class, 'update'])->name('bloques.update');
    Route::patch('/bloques/{bloque}', [BloqueController::class, 'update']); // opcional
    Route::delete('/bloques/{bloque}', [BloqueController::class, 'destroy'])->name('bloques.destroy');
    Route::post('bloques/reports', [BloqueController::class, 'reports'])->name('bloques.reports');

    //BLOQUE GEOM
    Route::get('/bloques_geom/{id}/geojson', [BloqueGeomController::class, 'geojson'])->name('bloques_geom.geojson')->whereNumber('id');

    //NICHOS
    Route::get('/nichos', [NichoController::class, 'index'])->name('nichos.index');
    Route::get('/nichos/create', [NichoController::class, 'create'])->name('nichos.create');
    Route::post('/nichos', [NichoController::class, 'store'])->name('nichos.store');
    Route::get('/nichos/{nicho}', [NichoController::class, 'show'])->name('nichos.show');
    Route::get('/nichos/{nicho}/edit', [NichoController::class, 'edit'])->name('nichos.edit');
    Route::put('/nichos/{nicho}', [NichoController::class, 'update'])->name('nichos.update');
    Route::delete('/nichos/{nicho}', [NichoController::class, 'destroy'])->name('nichos.destroy');
    Route::post('nichos/reports', [NichoController::class, 'reports'])->name('nichos.reports');
    // 1. Ruta para descargar/ver el QR del nicho. 
    Route::get('nichos/{nicho}/qr', [NichoController::class, 'downloadQr'])->name('nichos.qr');
    // 2. Ruta para Descargar la Imagen (Acción de descarga)
    Route::get('nichos/{nicho}/qr-image', [NichoController::class, 'downloadQrImage'])->name('nichos.qr.image');
    // Ruta PÚBLICA (simulada por ahora) donde aterriza el escaneo
    // Esta es la ruta que mostrará "Aquí yace Juan Perez"
    // Route::get('/ver-nicho/{uuid}', [NichoController::class, 'publicShow'])->name('public.nicho.info');


    //SOCIO-NICHO
    Route::get('/socio-nicho', [SocioNichoController::class, 'index'])->name('socio_nicho.index');
    Route::get('/socio-nicho/create', [SocioNichoController::class, 'create'])->name('socio_nicho.create');
    Route::post('/socio-nicho', [SocioNichoController::class, 'store'])->name('socio_nicho.store');
    Route::get('/socio-nicho/{socioNicho}', [SocioNichoController::class, 'show'])->name('socio_nicho.show');
    Route::get('/socio-nicho/{socioNicho}/edit', [SocioNichoController::class, 'edit'])->name('socio_nicho.edit');
    Route::put('/socio-nicho/{socioNicho}', [SocioNichoController::class, 'update'])->name('socio_nicho.update');
    Route::delete('/socio-nicho/{socioNicho}', [SocioNichoController::class, 'destroy'])->name('socio_nicho.destroy');
    //BENEFICIOS

    Route::get('/beneficios', [BeneficioController::class, 'index'])->name('beneficios.index');
    Route::get('/beneficios/create', [BeneficioController::class, 'create'])->name('beneficios.create');
    Route::post('/beneficios', [BeneficioController::class, 'store'])->name('beneficios.store');
    Route::get('/beneficios/{beneficio}', [BeneficioController::class, 'show'])->name('beneficios.show');
    Route::get('/beneficios/{beneficio}/edit', [BeneficioController::class, 'edit'])->name('beneficios.edit');
    Route::put('/beneficios/{beneficio}', [BeneficioController::class, 'update'])->name('beneficios.update');
    Route::delete('/beneficios/{beneficio}', [BeneficioController::class, 'destroy'])->name('beneficios.destroy');
    Route::post('beneficios/reports', [BeneficioController::class, 'reports'])->name('beneficios.reports');

    //SERVICIOS
    Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/create', [ServicioController::class, 'create'])->name('servicios.create');
    Route::post('/servicios', [ServicioController::class, 'store'])->name('servicios.store');
    Route::get('/servicios/{servicio}', [ServicioController::class, 'show'])->name('servicios.show');
    Route::get('/servicios/{servicio}/edit', [ServicioController::class, 'edit'])->name('servicios.edit');
    Route::put('/servicios/{servicio}', [ServicioController::class, 'update'])->name('servicios.update');
    Route::delete('/servicios/{servicio}', [ServicioController::class, 'destroy'])->name('servicios.destroy');
    Route::post('servicios/reports', [ServicioController::class, 'reports'])->name('servicios.reports');

    //FACTURAS
    // Listado de facturas
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    // Formulario para crear nueva factura
    Route::get('/facturas/create', [FacturaController::class, 'create'])->name('facturas.create');
    // Guardar nueva factura
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
    // Ver una factura específica
    Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
    // Formulario para editar factura
    Route::get('/facturas/{factura}/edit', [FacturaController::class, 'edit'])->name('facturas.edit');
    // Actualizar factura existente
    Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update');
    Route::put('/facturas/{factura}/emitir', [FacturaController::class, 'emitir'])->name('facturas.emitir');
    Route::put('/facturas/{factura}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'generarPdf'])->name('facturas.pdf');


    // ─── ASIGNACIONES (GESTIÓN DE NICHOS) ─────────────────────────────

    // 1. Listado principal (Index)
    Route::get('/asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');

    // 2. Formulario para Nueva Asignación (Modal Create)
    Route::get('/asignaciones/create', [AsignacionController::class, 'create'])->name('asignaciones.create');

    // 3. Guardar la Asignación (Store)
    Route::post('/asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');

    // 4. Ver detalle e historial (Modal Show)
    Route::get('/asignaciones/{id}', [AsignacionController::class, 'show'])->name('asignaciones.show');

    // 5. Formulario para Editar/Corregir (Modal Edit)
    Route::get('/asignaciones/{id}/edit', [AsignacionController::class, 'edit'])->name('asignaciones.edit');

    // 6. Actualizar los datos corregidos (Update)
    Route::put('/asignaciones/{id}', [AsignacionController::class, 'update'])->name('asignaciones.update');


    // ─── ACCIONES ESPECÍFICAS (EXHUMAR Y ELIMINAR) ────────────────────

    // Vista exclusiva para Exhumar (Formulario)
    Route::get('/asignaciones/{id}/exhumar', [AsignacionController::class, 'exhumarForm'])->name('asignaciones.exhumarForm');

    // Procesar la Exhumación (Guardar fecha de salida)
    Route::post('/asignaciones/exhumar', [AsignacionController::class, 'exhumar'])->name('asignaciones.exhumar');

    // Eliminar un registro por error (Destroy)
    Route::delete('/asignaciones/{nicho_id}/{fallecido_id}', [AsignacionController::class, 'destroy'])->name('asignacion.destroy');


    // ─── REPORTES PDF (ESTOS FALTABAN) ────────────────────────────────

    // 1. Reporte General de todos los nichos ocupados
    Route::get('/asignaciones/reporte-general', [AsignacionController::class, 'pdfReporteGeneral'])->name('asignaciones.pdf.general');

    // 2. Reporte de Historial de Exhumados
    Route::get('/asignaciones/reporte-exhumados', [AsignacionController::class, 'pdfReporteExhumados'])->name('asignaciones.pdf.exhumados');

    // 3. Certificado Individual de Exhumación
    Route::get('/asignaciones/certificado/{nicho_id}/{fallecido_id}', [AsignacionController::class, 'pdfCertificadoExhumacion'])->name('asignaciones.pdf.certificado');


    // ─── RUTAS PARA GESTIÓN DE PAGOS DE SOCIOS ────────────────────────
    Route::delete('pagos/{pago}', [App\Http\Controllers\PagoController::class, 'destroy'])->name('pagos.destroy');

    // Gestión de Pagos (Crear)
    Route::get('socios/{socio}/pagos', [App\Http\Controllers\PagoController::class, 'index'])->name('pagos.index');
    Route::post('socios/{socio}/pagos', [App\Http\Controllers\PagoController::class, 'store'])->name('pagos.store');

    // Gestión de Recibos (Historial, Ver, Editar, Borrar)
    Route::get('pagos-general', [App\Http\Controllers\PagoController::class, 'general'])->name('pagos.general');
    Route::get('pagos/nuevo-pago', [App\Http\Controllers\PagoController::class, 'create'])->name('pagos.create');
    Route::get('recibos/{recibo}', [App\Http\Controllers\PagoController::class, 'show'])->name('pagos.show');
    Route::get('recibos/{recibo}/edit', [App\Http\Controllers\PagoController::class, 'edit'])->name('pagos.edit');
    Route::put('recibos/{recibo}', [App\Http\Controllers\PagoController::class, 'update'])->name('pagos.update');
    Route::delete('recibos/{recibo}', [App\Http\Controllers\PagoController::class, 'destroy'])->name('pagos.destroy');
    // Ruta para ver el historial de un socio específico antes de cobrar
    Route::get('/pagos/historial/{socio}', [App\Http\Controllers\PagoController::class, 'historialSocio'])
        ->name('pagos.historial_socio');
});


// hasta aqui lo nuevo


Route::middleware(['auth', 'role.status:Administrador'])->group(function () {
    // Gestión de usuarios (Admin)
    Route::get('/user/users-management', [UserController::class, 'index'])->name('users-management');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/user/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::put('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    // El método unificado maneja ambas opciones: PDF y Excel
    Route::post('/users/reports', [UserController::class, 'generateReports'])->name('users.generateReports');

});

Route::middleware(['auth', 'role.status:Auditor'])->group(function () {
    Route::get('/audits', [AuditController::class, 'index'])->name('auditoria.index');
});





