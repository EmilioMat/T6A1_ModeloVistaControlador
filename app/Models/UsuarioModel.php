<?php

namespace App\Models;

class UsuarioModel extends Model
{
    protected $table1 = 'usuarios'; // Nombre de la tabla principal

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
}