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
}
