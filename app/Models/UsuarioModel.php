<?php

namespace App\Models;

use App\Database\Database;

class UsuarioModel extends Model
{
    protected $table = 'usuarios'; // Nombre de la tabla principal

    // Metodo para crear la tabla de usuarios si no existe
    public function definirTabla(): void
    {
        // Definir las columnas de la tabla
        $columns = [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'nombre' => 'VARCHAR(50) NOT NULL',
            'apellidos' => 'VARCHAR(50) NOT NULL',
            'nombre_usuario' => 'VARCHAR(50) NOT NULL',
            'email' => 'VARCHAR(100) UNIQUE NOT NULL',
            'fecha_nacimiento' => 'DATE NOT NULL',
            'contrasena' => 'VARCHAR(255) NOT NULL',
            'saldo' => 'DECIMAL(10, 2) DEFAULT 0.00',
        ];

        // Crear la tabla utilizando el método del padre
        $this->createTable($columns);
    }

    // Metodo para insertar datos de prueba
    public function insertarDatosPrueba(int $cantidad = 100): void
    {
        $db = Database::getConnection();

        for ($i = 0; $i < $cantidad; $i++) {
            // Generar valores de prueba aleatorios
            $nombre = 'Usuario_' . $i;
            $apellidos = 'Apellido_' . $i;
            $nombre_usuario = 'user_' . $i;
            $email = 'usuario_' . $i . '@ejemplo.com';

            // Generar fecha de nacimiento aleatoria
            $fecha_nacimiento = date('Y-m-d', strtotime('-' . rand(18, 50) . ' years'));

            // contrasena  "123456"
            $contrasena = password_hash('123456', PASSWORD_DEFAULT);

            // Generar saldo aleatorio entre 10 y 1000
            $saldo = rand(10, 1000);

            // Insertar los datos en la base de datos
            $sql = "INSERT INTO {$this->table} (nombre, apellidos, nombre_usuario, email, fecha_nacimiento, contrasena, saldo) VALUES (:nombre, :apellidos, :nombre_usuario, :email, :fecha_nacimiento, :contrasena, :saldo)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':contrasena', $contrasena);
            $stmt->bindParam(':saldo', $saldo);
            $stmt->execute();
        }

        // Guardar el mensaje en la sesión
        session_start();
        $_SESSION['mensaje'] = "Base de datos creada, se han insertado {$cantidad} usuarios de prueba con saldo aleatorio";

        header('Location: /');
        exit();
    }


    // Metodo para crear las tablas y poblar con datos de prueba
    public function crearTablasUsuarios(): void
    {
        // Crear la tabla si no existe
        $this->definirTabla();

        // Insertar datos de prueba
        $this->insertarDatosPrueba();
    }

    public function obtenerUsuarioPorNombre($nombreUsuario)
    {
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM usuarios WHERE nombre_usuario = :usuario";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':usuario', $nombreUsuario);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error al consultar el usuario: " . $e->getMessage());
        }
    }

    public function obtenerUsuarioPorEmail($email)
    {
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error al consultar el correo electrónico: " . $e->getMessage());
        }
    }

    public function obtenerUsuarioPorId($id)
    {
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error al consultar el usuario: " . $e->getMessage());
        }
    }

    public function updateUser($id, $nombre, $apellidos, $nombre_usuario, $email, $fecha_nacimiento)
    {
        try {
            // Obtención de la conexión a la base de datos
            $db = Database::getConnection();

            // Preparar la consulta SQL
            $sql = "UPDATE usuarios 
            SET nombre = :nombre, apellidos = :apellidos, nombre_usuario = :nombre_usuario, email = :email, fecha_nacimiento = :fecha_nacimiento 
            WHERE id = :id";
            $stmt = $db->prepare($sql);

            // Vincular los parámetros
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);

            // Ejecutar la consulta
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            // Si hay un error en la base de datos, lanzamos una excepción
            throw new \Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }


    // Registrar un nuevo usuario
    public function registrarUsuario($datos)
    {
        $sql = "INSERT INTO usuarios (nombre, apellidos, nombre_usuario, email, fecha_nacimiento, contrasena, saldo) 
                VALUES (:nombre, :apellidos, :nombre_usuario, :email, :fecha_nacimiento, :contrasena, :saldo)";
        $db = Database::getConnection();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellidos', $datos['apellidos']);
        $stmt->bindParam(':nombre_usuario', $datos['usuario']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':fecha_nacimiento', $datos['fecha_nacimiento']);
        $stmt->bindParam(':contrasena', $datos['contrasena']);
        $stmt->bindParam(':saldo', $datos['saldo']);

        $stmt->execute();
    }


    // Transacción para transferir saldo entre usuarios
    public function transferirSaldo($usuarioIdRemitente, $nombreUsuarioDestino, $saldo)
    {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();

            // Obtener el saldo del usuario remitente
            $sql = "SELECT saldo FROM usuarios WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $usuarioIdRemitente);
            $stmt->execute();
            $remitente = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$remitente || $remitente['saldo'] < $saldo) {
                throw new \Exception("Saldo insuficiente.");
            }

            // Restar saldo al usuario remitente
            $sql = "UPDATE usuarios SET saldo = saldo - :saldo WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':saldo', $saldo);
            $stmt->bindParam(':id', $usuarioIdRemitente);
            $stmt->execute();

            // Sumar saldo al usuario destinatario
            $sql = "UPDATE usuarios SET saldo = saldo + :saldo WHERE nombre_usuario = :nombreUsuario";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':saldo', $saldo);
            $stmt->bindParam(':nombreUsuario', $nombreUsuarioDestino);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new \Exception("El usuario destinatario no existe.");
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return $e->getMessage();
        }
    }


    public function getAllUsers()
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, nombre, apellidos, nombre_usuario, email FROM usuarios");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteUser($id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
    public function getAllUsersWithPagination($page = 1, $perPage = 5)
    {
        try {
            $db = Database::getConnection();

            // Calculate offset
            $offset = ($page - 1) * $perPage;

            // Get paginated users
            $stmt = $db->prepare("SELECT id, nombre, apellidos, nombre_usuario, email 
                             FROM usuarios 
                             ORDER BY id 
                             LIMIT :limit OFFSET :offset");

            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get total count of users
            $totalStmt = $db->query("SELECT COUNT(*) FROM usuarios");
            $total = $totalStmt->fetchColumn();

            return [
                'users' => $users,
                'total' => $total,
                'totalPages' => ceil($total / $perPage),
                'currentPage' => $page
            ];
        } catch (\PDOException $e) {
            throw new \Exception("Error al obtener usuarios: " . $e->getMessage());
        }
    }
}
