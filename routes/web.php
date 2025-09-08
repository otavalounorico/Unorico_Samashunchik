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

Route::get('/', function () {return redirect('/dashboard');})->middleware('auth');
Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard')->middleware('auth');
Route::get('/tables', function () {return view('tables');})->name('tables')->middleware('auth');
Route::get('/wallet', function () {return view('wallet');})->name('wallet')->middleware('auth');
Route::get('/RTL', function () {return view('RTL');})->name('RTL')->middleware('auth');
Route::get('/profile', function () {return view('account-pages.profile');})->name('profile')->middleware('auth');

Route::get('/signin', function () {return view('account-pages.signin');})->name('signin');
Route::get('/signup', function () {return view('account-pages.signup');})->name('signup')->middleware('guest');
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

Route::middleware(['auth','role:Administrador'])->group(function () {

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
Route::middleware(['auth','permission:ver auditoria'])->get('/audits', [AuditController::class, 'index'])->name('auditoria.index');

// hasta aqui lo nuevo

Route::middleware(['auth', 'role.status:Administrador'])->group(function () {
    // Gestión de usuarios (Admin)
    Route::get('/user/users-management', [UserController::class, 'index'])->name('users-management');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/user/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::put('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');


});



Route::middleware(['auth', 'role.status:Auditor'])->group(function () {
    Route::get('/audits', [AuditController::class, 'index'])->name('auditoria.index');
});





