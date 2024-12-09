<?php
// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$clave = '';
$base_datos = 'tarea4';

$conn = new mysqli($host, $usuario, $clave, $base_datos);

//comprobamos si la conexion ha sido exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

//si ha sido exitosa creamos la tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    apellidos VARCHAR(100),
    email VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'usuarios' creada correctamente.<br>";
} else {
    echo "Error al crear la tabla: " . $conn->error . "<br>";
}

// insertamos los datos de prueba
for ($i = 1; $i <= 100; $i++) {
    $nombre = "Usuario$i";
    $apellidos = "Apellido$i";
    $email = "usuario$i@ejemplo.com";
    $sql_insert = "INSERT INTO usuarios (nombre, apellidos, email) VALUES ('$nombre', '$apellidos', '$email')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "Registro $i insertado correctamente.<br>";
    } else {
        echo "Error al insertar el registro $i: " . $conn->error . "<br>";
    }
}

// Cerrar la conexión
$conn->close();
?>
