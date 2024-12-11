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

        // registrar el nuevo usuario
        try {
            $datosUsuario = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'usuario' => $usuario,
                'email' => $email,
                'fecha_nacimiento' => $fecha_nacimiento,
                'contrasena' => $contraseñaHash,
                'saldo' => $saldo
            ];

            $usuarioModel->registrarUsuario($datosUsuario);

            $_SESSION['mensaje'] = "Usuario registrado correctamente.";

            // Redirigir al dado de usuarios
            return $this->redirect('/main');
        } catch (\PDOException $e) {
            return $this->view('usuarios.registro', ['error' => 'Error al registrar el usuario: ' . $e->getMessage()]);
        }
    }

    public function mostrarPanel($id)
    {
if (isset($_SESSION['usuario_id'])) {
    if ($id == $_SESSION['usuario_id']) {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['mensaje'] = "Debe iniciar sesión para acceder a esta página.";
            return $this->view('/login');
        }

        // Verificar que el ID de la URL coincide con el usuario autenticado
        if ($_SESSION['usuario_id'] != $id) {
            return $this->view('usuarios.panel', ['error' => 'No tiene permisos para acceder a este panel.']);
        }

        // Obtener los datos del usuario
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->obtenerUsuarioPorId($id);

        if (!$usuario) {
            return $this->view('usuarios.panel', ['error' => 'El usuario no existe.']);
        }

        // Cargar la vista del panel con los datos del usuario
        return $this->view('usuarios.panel', ['usuario' => $usuario]);
    } else {
        return $this->view('home',  $_SESSION['mensaje'] = "No tienes acceso para acceder a este usuario");
    }
    
} else {
    return $this->view('home',  $_SESSION['mensaje'] = "No tienes acceso para acceder a este usuario");
}

    
    }

    public function actualizarUsuario()
    {
        $usuarioId = $_SESSION['usuario_id'];

        // Comprobar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST["id"] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $nombre_usuario = $_POST['nombre_usuario'] ?? '';
            $email = $_POST['email'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

            // Validaciones de campos
            if (empty($nombre) || empty($apellidos) || empty($nombre_usuario) || empty($email) || empty($fecha_nacimiento)) {
                return $this->view('usuarios.panel', [
                    'errores' => ['general' => 'Por favor, completa todos los campos.'],
                    'usuario' => $_POST
                ]);
            }

            try {
                // Instancia del modelo
                $usuarioModel = new UsuarioModel();

                // Llamar al método del modelo para actualizar los datos
                $usuarioModel->updateUser($id, $nombre, $apellidos, $nombre_usuario, $email, $fecha_nacimiento);

                $_SESSION['mensaje'] = "Datos actualizados correctamente.";
                header('Location: /main');
                exit();
            } catch (\Exception $e) {
                $_SESSION['mensaje'] = "Error al actualizar los datos: " . $e->getMessage();
                return $this->view('usuarios.panel', [
                    'errores' => ['general' => 'Error al procesar la solicitud: ' . $e->getMessage()],
                    'usuario' => $_POST
                ]);
            }
        }

        // Si no es POST, mostrar el formulario de edición
        return $this->view('usuarios.panel');
    }

    public function enviarSaldo()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['mensaje'] = "Debe iniciar sesión para enviar saldo.";
            return $this->redirect('/login');
        }

        // Comprobar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreUsuarioDestino = $_POST['usuarioDestino'] ?? '';
            $saldo = $_POST['saldo'] ?? 0;

            // Validar datos
            if (empty($nombreUsuarioDestino) || $saldo <= 0) {
                $_SESSION['mensaje'] = "Por favor, ingresa un saldo mayor a cero y un usuario válido.";
                return $this->redirect("/usuario/panel/{$_SESSION['usuario_id']}");
            }

            // Instanciar el modelo
            $usuarioModel = new UsuarioModel();

            // Transferir saldo
            $resultado = $usuarioModel->transferirSaldo($_SESSION['usuario_id'], $nombreUsuarioDestino, $saldo);

            if ($resultado === true) {
                $_SESSION['mensaje'] = "Se ha transferido $saldo $ de saldo a $nombreUsuarioDestino con éxito.";
            } else {
                $_SESSION['mensaje'] = "Error al realizar la transferencia: " . $resultado;
            }

            // Redirigir al home
            return $this->redirect('/main');
        }

        // Si no es POST, redirigir al home
        return $this->redirect('/main');
    }

    public function listarUsuarios()
    {
        try {
            // Get current page from URL parameter, default to 1 if not set
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;

            // Ensure page is at least 1
            if ($currentPage < 1) {
                $currentPage = 1;
            }

            $usuarioModel = new UsuarioModel();
            $result = $usuarioModel->getAllUsersWithPagination($currentPage, 5);

            return $this->view('usuarios.lista', [
                'usuarios' => $result['users'],
                'totalPages' => $result['totalPages'],
                'currentPage' => $result['currentPage']
            ]);
        } catch (\Exception $e) {
            die("Error al cargar la lista de usuarios: " . $e->getMessage());
        }
    }

    public function editarUsuario()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("El ID del usuario es requerido.");
        }

        try {
            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->obtenerUsuarioPorId($id);

            if (!$usuario) {
                die("Usuario no encontrado.");
            }

            return $this->view('usuarios.panel', ['usuario' => $usuario]);
        } catch (\Exception $e) {
            die("Error al cargar el usuario: " . $e->getMessage());
        }
    }

    public function eliminarUsuario()
    {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            die("El ID del usuario es requerido.");
        }

        try {
            $usuarioModel = new UsuarioModel();
            $usuarioModel->deleteUser($id);
            $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
            $this->redirect('/main');
            exit();
        } catch (\Exception $e) {
            die("Error al eliminar el usuario: " . $e->getMessage());
        }
    }


    public function logout()
    {
        session_destroy();

        // Redirigir al usuario a la página principal
        header("Location: /");
        exit();
    }
}
