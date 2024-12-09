<?php

namespace App\Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;  // Importar Dotenv correctamente

class Database
{
    private static $connection;

    public static function getConnection(): PDO
    {
        if (!self::$connection) {
            // Cargar las variables de entorno desde el archivo .env
            $dotenv = Dotenv::createImmutable(__DIR__.'/../../');  // Ruta correcta al archivo .env
            $dotenv->load();

            // Obtener las variables de entorno
            $host = $_ENV['DB_HOST'];
            $db = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            try {
                // Crear la conexión con la base de datos
                self::$connection = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Error al conectar a la base de datos: " . $e->getMessage();
            }
        }

        return self::$connection;
    }
}
