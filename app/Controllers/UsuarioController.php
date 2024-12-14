<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Database\Database;

class UsuarioController extends Controller
{
    /*
    *
    * Metodo --> Para crear la base de datos
    *
    */
    public function crearBaseDeDatos(): void
    {
        $usuarioModel = new UsuarioModel();

        if ($usuarioModel->baseDeDatosExistente()) {
            session_start();
            $_SESSION['mensaje'] = 'La base de datos ya estaba creada.';
            header('Location: /');
            exit();
        }

        $usuarioModel->crearTablasUsuarios();
        echo "Base de datos creada y poblada con datos de prueba.";
    }

    /*
    *
    * Metodo --> Para mostrar el formulario del login
    *
    */
    public function login()
    {
        return $this->view("usuarios.login");
    }

    /*
    *
    * Metodo --> Que verifica el login sea correcto
    *
    */
    public function verificarLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['usuario'] ?? '');
            $contrasena = trim($_POST['contraseña'] ?? '');

            // Validaciones de los campos
            $errores = [];

            if (empty($usuario) || empty($contrasena)) {
                $errores['usuario'] = 'Por vafor complete todos los campos';
            } else {
                if (strlen($usuario) < 3 || strlen($usuario) > 20) {
                    $errores['usuario'] = 'El nombre es obligatorio y debe tener entre 3 y 20 caracteres.';
                }
                if (!preg_match('/^\d{6,}$/', $contrasena)) {
                    $errores['contrasena'] = 'La contraseña debe tener al menos 6 números y no puede contener letras ni caracteres especiales.';
                }
            }

            // Si hay errores, regresar a la vista con los mensajes
            if (!empty($errores)) {
                return $this->view('usuarios.login', ['errores' => $errores]);
            } else {
                // Verificación con la base de datos
                try {
                    $usuarioModel = new UsuarioModel();
                    $usuarioDB = $usuarioModel->obtenerUsuarioPorNombre($usuario);

                    if ($usuarioDB && password_verify($contrasena, $usuarioDB['contrasena'])) {
                        // Iniciar sesión
                        $_SESSION['usuario_id'] = $usuarioDB['id'];
                        $_SESSION['nombre_usuario'] = $usuarioDB['nombre_usuario'];
                        $_SESSION['mensaje'] = "Bienvenido, " . $_SESSION['nombre_usuario'];
                        header('Location: /main');
                        exit();
                    } else {
                        return $this->view('usuarios.login', ['errores' => ['general' => 'Usuario o contraseña incorrectos.']]);
                    }
                } catch (\Exception $e) {
                    return $this->view('usuarios.login', ['errores' => ['general' => 'Error al procesar la solicitud: ' . $e->getMessage()]]);
                }
            }
        }

        return $this->view('usuarios.login');
    }

    /*
    *
    * Metodo --> Para mostrar el panel de registro de usuario
    *
    */
    public function mostrarRegistro()
    {
        return $this->view('usuarios.registro');
    }

    /*
    *
    * Metodo --> Que verifica que el registro sea correcto
    *
    */
    public function verificarRegistro()
    {
        $errores = []; // Array para almacenar los errores
        $data = [
            'nombre' => '',
            'apellidos' => '',
            'nombre_usuario' => '',
            'email' => '',
            'fecha_nacimiento' => '',
            'saldo' => '',
            'errores' => $errores,
        ];

        // Validación al enviar el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidos = isset($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
            $nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
            $fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? trim($_POST['fecha_nacimiento']) : '';
            $saldo = isset($_POST['saldo']) ? trim($_POST['saldo']) : '';


            if (empty($nombre) || empty($apellidos) || empty($nombre_usuario) || empty($email) || empty($contrasena) || empty($fecha_nacimiento) || empty($saldo)) {
                $errores['nombre'] = 'Por vafor complete todos los campos';
            } else {
                if (strlen($nombre) < 3 || strlen($nombre) > 20) {
                    $errores['nombre'] = 'El nombre es obligatorio y debe tener entre 3 y 20 caracteres.';
                }
                if (strlen($apellidos) > 20) {
                    $errores['apellidos'] = 'Los apellidos no pueden tener mas de 20 caracteres';
                }
                if (strlen($nombre_usuario) > 20) {
                    $errores['nombre_usuario'] = 'El nombre_usuario no pueden tener mas de 20 caracteres';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errores['email'] = 'Debe proporcionar un correo electrónico válido.';
                }
                if (!preg_match('/^\d+$/', $contrasena)) {
                    $errores['contrasena'] = 'La contraseña solo puede tener números.';
                }
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_nacimiento)) {
                    $errores['fecha_nacimiento'] = 'Debe proporcionar una fecha válida.';
                }
                if (!preg_match('/^[1-9][0-9]*$/', $saldo)) {
                    $errores['saldo'] = 'El saldo debe ser un número entero positivo';
                }
            }
            // Verificar si no hay errores
            if (empty($errores)) {
                $usuarioModel = new UsuarioModel();

                // Verificar si el usuario ya existe
                if ($usuarioModel->obtenerUsuarioPorNombre($nombre_usuario)) {
                    $errores['nombre_usuario'] = 'El nombre de usuario ya está registrado.';
                }

                if ($usuarioModel->obtenerUsuarioPorEmail($email)) {
                    $errores['email'] = 'El correo electrónico ya está registrado.';
                }

                // Si todo está bien, registramos al usuario
                if (empty($errores)) {
                    try {
                        $datosUsuario = [
                            'nombre' => $nombre,
                            'apellidos' => $apellidos,
                            'nombre_usuario' => $nombre_usuario,
                            'email' => $email,
                            'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
                            'fecha_nacimiento' => $fecha_nacimiento,
                            'saldo' => $saldo,
                        ];
                        $usuarioModel->registrarUsuario($datosUsuario);

                        $_SESSION['mensaje'] = 'Usuario registrado correctamente.';
                        return $this->redirect('/main');
                    } catch (\PDOException $e) {
                        $errores['general'] = 'Error al registrar el usuario: ' . $e->getMessage();
                    }
                }
            }

            // Enviamos los datos y errores al formulario
            $data = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'nombre_usuario' => $nombre_usuario,
                'email' => $email,
                'fecha_nacimiento' => $fecha_nacimiento,
                'saldo' => $saldo,
                'errores' => $errores,
            ];
        }

        // Cargamos la vista con los datos y errores
        return $this->view('usuarios.registro', $data);
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

                // Cargamos la vista del panel con los datos del usuario
                return $this->view('usuarios.panel', ['usuario' => $usuario]);
            } else {
                return $this->view('home',  $_SESSION['mensaje'] = "No tienes acceso para acceder a este usuario");
            }
        } else {
            return $this->view('home',  $_SESSION['mensaje'] = "No tienes acceso para acceder a este usuario");
        }
    }

    /*
    *
    * Metodo --> Que nos permite actualizar los campos de un usuario mediante su formulario
    *
    */
    public function actualizarUsuario()
    {
        $error = [];
        $usuarioId = $_SESSION['usuario_id'];

        // Comprobar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST["id"] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $nombre_usuario = $_POST['nombre_usuario'] ?? '';
            $email = $_POST['email'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
            $saldo = $_POST['saldo'] ?? '';

            if (empty($nombre) || empty($apellidos) || empty($nombre_usuario) || empty($email)  || empty($fecha_nacimiento) || empty($saldo)) {
                $error['nombre'] = 'Por vafor complete todos los campos';
            } else {
                if (strlen($nombre) < 3 || strlen($nombre) > 20) {
                    $error['nombre'] = 'El nombre es obligatorio y debe tener entre 3 y 20 caracteres.';
                }
                if (strlen($apellidos) > 20) {
                    $error['apellidos'] = 'Los apellidos no pueden tener mas de 20 caracteres';
                }
                if (strlen($nombre_usuario) > 20) {
                    $error['nombre_usuario'] = 'Los apellidos no pueden tener mas de 20 caracteres';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = 'Debe proporcionar un correo electrónico válido.';
                }
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_nacimiento)) {
                    $error['fecha_nacimiento'] = 'Debe proporcionar una fecha válida.';
                }
                if (!preg_match('/^\d+$/', $saldo)) {
                    $error['saldo'] = 'Debe proporcionar un saldo válido.';
                }
            }

            // Si hay errores, regresar a la vista con los mensajes
            if (!empty($error)) {
                return $this->view('usuarios.panel', ['error' => $error, 'usuario' => $_POST]);
            } else {
                try {
                    // Instancia del modelo
                    $usuarioModel = new UsuarioModel();

                    // Llamar al método del modelo para actualizar los datos
                    $usuarioModel->updateUser($usuarioId, $nombre, $apellidos, $nombre_usuario, $email, $fecha_nacimiento, $saldo);

                    $_SESSION['mensaje'] = "Datos actualizados correctamente.";
                    header('Location: /');
                    exit();
                } catch (\Exception $e) {
                    $_SESSION['mensaje'] = "Error al actualizar los datos: " . $e->getMessage();
                    return $this->view('usuarios.panel', [
                        'error' => ['general' => 'Error al procesar la solicitud: ' . $e->getMessage()],
                        'usuario' => $_POST
                    ]);
                }
            }
        }

        // Si no es POST, mostrar el formulario de edición
        return $this->view('usuarios.panel');
    }



    /*
    *
    * Metodo --> Que nos permite enviar saldo a otro usuario sabiendo su nombre
    *
    */
    public function enviarSaldo()
    {
        // Verifico si el usuario ha iniciado sesión
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['mensaje'] = "Debe iniciar sesión para enviar saldo.";
            return $this->redirect('/login');
        }

        // Verifico si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombreUsuarioDestino = $_POST['usuarioDestino'] ?? '';
            $saldo = $_POST['saldo'] ?? 0;

            // Validacion de los datos
            if (empty($nombreUsuarioDestino) || empty($saldo) || $saldo <= 0) {
                $_SESSION['mensaje'] = "Por favor, ingresa un saldo mayor a cero y un usuario válido.";
                return $this->redirect("/usuario/{$_SESSION['usuario_id']}");
            }

            $usuarioModel = new UsuarioModel();

            // Comprobar si el usuario destino existe en la base de datos
            $usuarioDestinoExistente = $usuarioModel->obtenerUsuarioPorNombre($nombreUsuarioDestino);

            if (!$usuarioDestinoExistente) {
                $_SESSION['mensaje'] = "El usuario destinatario no existe.";
                return $this->redirect("/usuario/{$_SESSION['usuario_id']}");
            }

            // Transferir saldo
            $resultado = $usuarioModel->transferirSaldo($_SESSION['usuario_id'], $nombreUsuarioDestino, $saldo);

            if ($resultado === true) {
                $_SESSION['mensaje'] = "Se ha transferido $saldo $ a $nombreUsuarioDestino con éxito.";
            } else {
                $_SESSION['mensaje'] = "Error al realizar la transferencia: " . $resultado;
            }

            return $this->redirect('/');
        }

        // Si no es un POST, redirigir al home
        return $this->redirect('/');
    }

    /*
    *
    * Metodo --> Que nos pemite listar los datos con una paginacion incluida de 5 en 5
    *
    */
    public function listarUsuarios()
    {
        try {
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;

            $filters = array_filter([
                'nombre' => $_GET['nombre'] ?? null,
                'apellidos' => $_GET['apellidos'] ?? null,
                'nombre_usuario' => $_GET['nombre_usuario'] ?? null,
                'email' => $_GET['email'] ?? null,
                'fecha_nacimiento' => $_GET['fecha_nacimiento'] ?? null,
                'saldo_min' => $_GET['saldo_min'] ?? null,
                'saldo_max' => $_GET['saldo_max'] ?? null,
            ]);

            $usuarioModel = new UsuarioModel();
            $result = $usuarioModel->getAllUsersWithPagination($currentPage, 5, $filters);

            return $this->view('usuarios.lista', [
                'usuarios' => $result['users'],
                'totalPages' => $result['totalPages'],
                'currentPage' => $result['currentPage'],
                'filters' => $filters,
            ]);
        } catch (\Exception $e) {
            die("Error al cargar la lista de usuarios: " . $e->getMessage());
        }
    }

    /*
    *
    * Metodo --> Que nos permite editar los campos de un usuario
    *
    */
    public function editarUsuario($id)
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

    /*
    *
    * Metodo --> Que nos permite eliminar un usuario de la base de datos
    *
    */
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

    /*
    *
    * Metodo --> Que nos permite cerrar sesion
    *
    */
    public function logout()
    {
        session_destroy();

        header("Location: /");
        exit();
    }
}
