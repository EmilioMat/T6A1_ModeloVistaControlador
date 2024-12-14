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

// Ruta para iniciar sesión
Route::post('/login', [UsuarioController::class, 'verificarLogin']);

// Ruta para redirigir a inicio cuando inicie sesión
Route::get('/main', [HomeController::class, 'index']);

// Ruta para mostrar el formulario de registro
Route::get('/registro', [UsuarioController::class, 'mostrarRegistro']);

// Ruta para procesar el registro (POST)
Route::post('/registro', [UsuarioController::class, 'verificarRegistro']);

// Ruta para ver el panel de usuario
Route::get('/usuario/:id', [UsuarioController::class, 'mostrarPanel']);



// Ruta para poder enviar saldo a otro usuario
Route::post('/usuario/enviar-saldo', [UsuarioController::class, 'enviarSaldo']);

Route::get('/usuarios', [UsuarioController::class, 'listarUsuarios']);
Route::get('/usuarios/editar', [UsuarioController::class, 'editarUsuario']);
Route::post('/usuarios/eliminar', [UsuarioController::class, 'eliminarUsuario']);

//Ruta para modificar los datos del usuario
Route::post('/usuario/editar', [UsuarioController::class, 'actualizarUsuario']);


// Ruta para cerrar sesión
Route::get('/logout', [UsuarioController::class, 'logout']);

// Despachar las rutas
Route::dispatch();
