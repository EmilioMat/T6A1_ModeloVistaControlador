<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Database\Database;

class UsuarioController extends Controller
{
    public function index()
    {
        // Crear la conexión y acceder al modelo
        $usuarioModel = new UsuarioModel();
        $usuarios = $usuarioModel->consultaPrueba();

        // Pasar los usuarios a la vista
        return $this->view('usuarios.index', ['usuarios' => $usuarios]);
    }

    public function create()
    {
        return $this->view('usuarios.create');
    }

    public function store()
    {
        // Recoger los datos enviados por el formulario
        $usuarioModel = new UsuarioModel();
        var_dump($_POST); // Debug
        echo "Datos enviados desde POST.";

        // Redirigir después de procesar los datos
        // return $this->redirect('/usuarios');
    }

    public function show($id)
    {
        echo "Mostrar usuario con ID: {$id}";
    }

    public function edit($id)
    {
        echo "Editar usuario";
    }

    public function update($id)
    {
        echo "Actualizar usuario";
    }

    public function destroy($id)
    {
        echo "Borrar usuario";
    }

    // Ejemplo de consulta con SQL Query Builder
    public function pruebasSQLQueryBuilder()
    {
        $usuarioModel = new UsuarioModel();
        // Ejemplo de consultas SQL comentadas
        // $usuarioModel->all();
        // $usuarioModel->select('columna1', 'columna2')->get();
        echo "Pruebas SQL Query Builder";
    }

    // Crear base de datos y poblarla con datos de prueba
    public function crearBaseDeDatos(): void
    {
        $usuarioModel = new UsuarioModel();
        $usuarioModel->crearTablasUsuarios();
        echo "Base de datos creada y poblada con datos de prueba.";
    }

    // Método para mostrar el formulario de login
    public function login()
    {
        return $this->view("usuarios.login");
    }

    // Verificar el login y redirigir
    public function verificarLogin()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'] ?? null;
            $contrasena = $_POST['contraseña'] ?? null;

            if (empty($usuario) || empty($contrasena)) {
                return $this->view('usuarios.login', ['error' => 'Por favor, completa todos los campos.']);
            }

            try {
                $usuarioModel = new UsuarioModel();
                $usuarioDB = $usuarioModel->obtenerUsuarioPorNombre($usuario);

                if ($usuarioDB && password_verify($contrasena, $usuarioDB['contrasena'])) {
                    $_SESSION['usuario_id'] = $usuarioDB['id'];
                    $_SESSION['nombre_usuario'] = $usuarioDB['nombre_usuario'];
                    $_SESSION['mensaje'] = "Bienvenido, " . $_SESSION['nombre_usuario'];
                    header('Location: /main');
                    exit();
                } else {
                    return $this->view('usuarios.login', ['error' => 'Usuario o contraseña incorrectos.']);
                }
            } catch (\Exception $e) {
                return $this->view('usuarios.login', ['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
            }
        }

        return $this->view('usuarios.login');
    }

    // Mostrar formulario de registro
    public function mostrarRegistro()
    {
        return $this->view('usuarios.registro');
    }

    public function verificarRegistro()
    {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $usuario = $_POST['usuario'];
        $email = $_POST['email'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $contraseña = $_POST['contraseña'];
        $saldo = $_POST['saldo'];

        // Instanciar el modelo de usuario
        $usuarioModel = new UsuarioModel();

        // Verificar si el nombre de usuario ya está registrado
        $usuarioExistente = $usuarioModel->obtenerUsuarioPorNombre($usuario);
        if ($usuarioExistente) {
            return $this->view('usuarios.registro', ['error' => 'El nombre de usuario ya está registrado.']);
        }

        // Verificar si el correo electrónico ya está registrado
        $emailExistente = $usuarioModel->obtenerUsuarioPorEmail($email);
        if ($emailExistente) {
            return $this->view('usuarios.registro', ['error' => 'El correo electrónico ya está registrado.']);
        }

        // Crear el hash de la contraseña
        $contraseñaHash = password_hash($contraseña, PASSWORD_DEFAULT);

        // Intentar registrar el nuevo usuario
        try {
            // Consulta SQL para insertar los datos del nuevo usuario
            $sql = "INSERT INTO usuarios (nombre, apellidos, nombre_usuario, email, fecha_nacimiento, contrasena, saldo) 
                VALUES (:nombre, :apellidos, :nombre_usuario, :email, :fecha_nacimiento, :contrasena, :saldo)";

            // Conexión a la base de datos
            $db = Database::getConnection();
            $stmt = $db->prepare($sql);

            // Vincular los parámetros
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':nombre_usuario', $usuario);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':contrasena', $contraseñaHash);
            $stmt->bindParam(':saldo', $saldo);

            // Ejecutar la consulta
            $stmt->execute();

            // Mensaje de éxito
            $_SESSION['mensaje'] = "Usuario registrado correctamente.";

            // Redirigir al listado de usuarios (o donde desees)
            return $this->redirect('/main');
        } catch (\PDOException $e) {
            // Manejar errores de la base de datos
            return $this->view('usuarios.registro', ['error' => 'Error al registrar el usuario: ' . $e->getMessage()]);
        }
    }
}
