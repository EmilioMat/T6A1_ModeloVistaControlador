<?php

namespace App\Models;

use App\Database\Database; // Asegúrate de importar la clase Database si la usas para interactuar con la base de datos

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
            'email' => 'VARCHAR(100) UNIQUE NOT NULL',
            'saldo' => 'DECIMAL(10, 2) DEFAULT 0.00',
            'fecha_creacion' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ];

        // Crear la tabla utilizando el método del padre
        $this->createTable($columns);
    }

    // Metodo para insertar datos de prueba
    public function insertarDatosPrueba(int $cantidad = 100): void
    {
        // Instanciar el objeto de base de datos (usando PDO o el ORM de tu preferencia)
        $db = Database::getConnection(); // Asegúrate de tener una clase para la conexión a la base de datos

        for ($i = 0; $i < $cantidad; $i++) {
            // Generar valores de prueba aleatorios
            $nombre = 'Usuario_' . $i;
            $email = 'usuario_' . $i . '@ejemplo.com';

            // Generar saldo aleatorio entre 10 y 1000 (puedes ajustar el rango según lo necesites)
            $saldo = rand(10, 1000); // Genera un número aleatorio entre 10 y 1000

            // Insertar los datos en la base de datos
            $sql = "INSERT INTO {$this->table} (nombre, email, saldo) VALUES (:nombre, :email, :saldo)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':saldo', $saldo);
            $stmt->execute();
        }


        // Guardar el mensaje en la sesión
        session_start();
        $_SESSION['mensaje'] = "Base de datos creada, se han insertado {$cantidad} usuarios de prueba con saldo aleatorio.";

        //echo "Se han insertado {$cantidad} usuarios de prueba con saldo aleatorio.";

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
}
