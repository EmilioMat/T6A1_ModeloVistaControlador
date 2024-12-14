<?php

namespace App\Models;

use App\Database\Database;

class UsuarioModel extends Model
{
    protected $table = 'usuarios'; // Nombre de la tabla principal

    /*
    *
    * Metodo --> para crear la tabla de usuarios si no existe
    *
    */
    public function definirTabla(): void
    {
        // definimos las columnas de la tabla
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

        $this->createTable($columns);
    }

    /*
    *
    * Metodo --> para insertar datos de prueba
    *
    */
    public function insertarDatosPrueba(int $cantidad = 100): void
    {
        $db = Database::getConnection();

        for ($i = 0; $i < $cantidad; $i++) {
            // Generar valores de prueba aleatorios
            $nombre = 'Usuario_' . $i;
            $apellidos = 'Apellido_' . $i;
            $nombre_usuario = 'user_' . $i;
            $email = 'usuario_' . $i . '@ejemplo.com';

            // Compruebo si el email ya existe en la base de datos
            $checkEmailQuery = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
            $stmt = $db->prepare($checkEmailQuery);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            // Si el email ya existe, continuar con el siguiente
            if ($count > 0) {
                continue;
            }

            // Generaro fecha de nacimiento aleatoria
            $fecha_nacimiento = date('Y-m-d', strtotime('-' . rand(18, 50) . ' years'));

            // Contraseña "123456"
            $contrasena = password_hash('123456', PASSWORD_DEFAULT);

            // Generaro saldo aleatorio entre 10 y 1000
            $saldo = rand(10, 1000);

            // Inserto los datos en la base de datos
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

        session_start();
        $_SESSION['mensaje'] = "Base de datos creada, se han insertado {$cantidad} usuarios de prueba con saldo aleatorio.";

        header('Location: /');
        exit();
    }

    /*
    *
    * Metodo --> para crear las tablas y poblar con datos de prueba
    *
    */
    public function crearTablasUsuarios(): void
    {
        // Crear la tabla si no existe
        $this->definirTabla();

        // Insertar datos de prueba
        $this->insertarDatosPrueba();
    }

    /*
    *
    * Metodo --> para comprobar si la base de datos ya existe
    *
    */
    public function baseDeDatosExistente(): bool
    {
        $db = Database::getConnection();

        // Comprobar si la tabla 'usuarios' existe
        $query = "SHOW TABLES LIKE 'usuarios'";
        $stmt = $db->prepare($query);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /*
    *
    * Metodo --> para obtener un usuario por su nombre
    *
    */
    public function obtenerUsuarioPorNombre($nombreUsuario)
    {
        try {
            $db = Database::getConnection();
            $sql = "SELECT * FROM usuarios WHERE nombre_usuario = :usuario";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':usuario', $nombreUsuario);
            $stmt->execute();

            // Si se encuentra el usuario, devuelve los datos, de lo contrario, false
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: false;
        } catch (\PDOException $e) {
            throw new \Exception("Error al consultar el usuario: " . $e->getMessage());
        }
    }

    /*
    *
    * Metodo --> para obtener un usuario por su email
    *
    */
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

    /*
    *
    * Metodo --> para obtener un usuario por su id
    *
    */
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

    /*
    *
    * Metodo --> para poder actualizar los datos de un usuario
    *
    */
    public function updateUser($id, $nombre, $apellidos, $nombre_usuario, $email, $fecha_nacimiento, $saldo)
    {
        try {
            // Obtención de la conexión a la base de datos
            $db = Database::getConnection();

            $sql = "UPDATE usuarios 
        SET nombre = :nombre, apellidos = :apellidos, nombre_usuario = :nombre_usuario, email = :email, fecha_nacimiento = :fecha_nacimiento, saldo = :saldo 
        WHERE id = :id";
            $stmt = $db->prepare($sql);

            // Vincular los parámetros
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
            $stmt->bindParam(':saldo', $saldo);

            // Ejecutar la consulta
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            // Si hay un error en la base de datos, lanzamos una excepción
            throw new \Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }


    /*
    *
    * Metodo --> te permite insertar un nuevo usuario en la base de datos
    *
    */
    public function registrarUsuario($datos)
    {
        $sql = "INSERT INTO usuarios (nombre, apellidos, nombre_usuario, email, fecha_nacimiento, contrasena, saldo) 
                VALUES (:nombre, :apellidos, :nombre_usuario, :email, :fecha_nacimiento, :contrasena, :saldo)";
        $db = Database::getConnection();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellidos', $datos['apellidos']);
        $stmt->bindParam(':nombre_usuario', $datos['nombre_usuario']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':fecha_nacimiento', $datos['fecha_nacimiento']);
        $stmt->bindParam(':contrasena', $datos['contrasena']);
        $stmt->bindParam(':saldo', $datos['saldo']);

        $stmt->execute();
    }

    /*
    *
    * Metodo --> Transacción para transferir saldo entre usuarios
    *
    */
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

    /*
    *
    * Metodo --> Para borra un usuario mediante su ID
    *
    */
    public function deleteUser($id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
    /*
    *
    * Metodo --> Que obtiene todos los usuarios de la base de datos con una paginacion de 5 en 5
    *
    */
    public function getAllUsersWithPagination($page = 1, $perPage = 5, $filters = [])
    {
        try {
            // Obtener la conexión a la base de datos
            $db = Database::getConnection();

            // Iniciar la consulta SQL base
            $query = "SELECT id, nombre, apellidos, nombre_usuario, email, fecha_nacimiento, saldo 
                 FROM usuarios 
                 WHERE 1=1";

            $params = [];

            // Aplicamos todos los filtros
            if (!empty($filters['nombre'])) {
                $query .= " AND nombre LIKE :nombre";
                $params[':nombre'] = '%' . $filters['nombre'] . '%';
            }

            if (!empty($filters['apellidos'])) {
                $query .= " AND apellidos LIKE :apellidos";
                $params[':apellidos'] = '%' . $filters['apellidos'] . '%';
            }

            if (!empty($filters['nombre_usuario'])) {
                $query .= " AND nombre_usuario LIKE :nombre_usuario";
                $params[':nombre_usuario'] = '%' . $filters['nombre_usuario'] . '%';
            }

            if (!empty($filters['email'])) {
                $query .= " AND email LIKE :email";
                $params[':email'] = '%' . $filters['email'] . '%';
            }

            if (!empty($filters['fecha_nacimiento'])) {
                $query .= " AND fecha_nacimiento = :fecha_nacimiento";
                $params[':fecha_nacimiento'] = $filters['fecha_nacimiento'];
            }

            if (!empty($filters['saldo_min'])) {
                $query .= " AND saldo >= :saldo_min";
                $params[':saldo_min'] = $filters['saldo_min'];
            }

            if (!empty($filters['saldo_max'])) {
                $query .= " AND saldo <= :saldo_max";
                $params[':saldo_max'] = $filters['saldo_max'];
            }

            // Añadir orden y paginación a la consulta
            $query .= " ORDER BY id LIMIT :limit OFFSET :offset";

            // Calcular el desplazamiento según la página actual
            $offset = ($page - 1) * $perPage;

            // Preparar la consulta SQL con los parámetros
            $stmt = $db->prepare($query);

            // Asignar los valores a los parámetros
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            // Asignar los valores para la paginación
            $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

            // Ejecutar la consulta y obtener los usuarios
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Preparar una consulta para contar el total de usuarios según los filtros aplicados
            $countQuery = str_replace(
                'SELECT id, nombre, apellidos, nombre_usuario, email, fecha_nacimiento, saldo',
                'SELECT COUNT(*)',
                substr($query, 0, strpos($query, 'ORDER BY'))
            );
            $countStmt = $db->prepare($countQuery);

            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }

            $countStmt->execute();
            $total = $countStmt->fetchColumn();  // Obtener el total de usuarios filtrados
            return [
                'users' => $users,
                'total' => $total,
                'totalPages' => ceil($total / $perPage),  // Calcular el numero total de paginas
                'currentPage' => $page
            ];
        } catch (\PDOException $e) {
            throw new \Exception("Error al obtener usuarios: " . $e->getMessage());
        }
    }
}
