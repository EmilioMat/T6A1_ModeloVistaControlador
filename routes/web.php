<?php

use Lib\Route;
use App\Controllers\HomeController;
use App\Controllers\UsuarioController;

// Definir las rutas utilizando la clase Route
Route::get('/', [HomeController::class, 'index']);
Route::get('/usuario/nuevo', [UsuarioController::class, 'create']);
Route::get('/usuario', [UsuarioController::class, 'index']);
Route::get('/usuario/pruebas', [UsuarioController::class, 'pruebasSQLQueryBuilder']);
Route::get('/usuario/:id', [UsuarioController::class, 'show']);
Route::post('/usuario', [UsuarioController::class, 'store']);

// Nueva ruta para inicializar la tabla de usuarios
Route::get('/usuario/crear-base', [UsuarioController::class, 'crearBaseDeDatos']);



// Ruta para mostrar el formulario de login
Route::get('/login', [UsuarioController::class, 'login']);

//Ruta para iniciar sesion
Route::post('/login', [UsuarioController::class, 'verificarLogin']);

// Ruta para redirigir a inicio cuando incie sesion
Route::get('/main', [HomeController::class, 'index']);


// Despachar las rutas
Route::dispatch();
