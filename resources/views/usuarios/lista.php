<?php
// Validación
$errors = [];
$filters = [
    'nombre' => '',
    'apellidos' => '',
    'nombre_usuario' => '',
    'email' => '',
    'saldo_min' => '',
    'saldo_max' => '',
];

// Comprobamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validar nombre (solo letras y espacios)
    if (!empty($_GET['nombre']) && !preg_match('/^[a-zA-Z\s]+$/', $_GET['nombre'])) {
        $errors['nombre'] = "El nombre solo puede contener letras y espacios.";
    } else {
        $filters['nombre'] = $_GET['nombre'] ?? '';
    }

    // Validar apellidos (solo letras y espacios)
    if (!empty($_GET['apellidos']) && !preg_match('/^[a-zA-Z\s]+$/', $_GET['apellidos'])) {
        $errors['apellidos'] = "Los apellidos solo pueden contener letras y espacios.";
    } else {
        $filters['apellidos'] = $_GET['apellidos'] ?? '';
    }

    // Validar usuario (solo letras, números y guiones bajos)
    if (!empty($_GET['nombre_usuario']) && !preg_match('/^[a-zA-Z0-9_]+$/', $_GET['nombre_usuario'])) {
        $errors['nombre_usuario'] = "El usuario solo puede contener letras, números y guiones bajos.";
    } else {
        $filters['nombre_usuario'] = $_GET['nombre_usuario'] ?? '';
    }

    // Validar email
    if (!empty($_GET['email']) && !filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Por favor, introduce un email válido.";
    } else {
        $filters['email'] = $_GET['email'] ?? '';
    }

    // Validar saldo (debe ser numérico)
    if (!empty($_GET['saldo_min']) && !preg_match('/^\d+(\.\d+)?$/', $_GET['saldo_min'])) {
        $errors['saldo_min'] = "El saldo mínimo debe ser un número válido.";
    } else {
        $filters['saldo_min'] = $_GET['saldo_min'] ?? '';
    }

    // Validar saldo máximo (debe ser numérico)
    if (!empty($_GET['saldo_max']) && !preg_match('/^\d+(\.\d+)?$/', $_GET['saldo_max'])) {
        $errors['saldo_max'] = "El saldo máximo debe ser un número válido.";
    } else {
        $filters['saldo_max'] = $_GET['saldo_max'] ?? '';
    }

    // Aquí es donde se filtrarían los usuarios según los filtros, si no hay errores.
    if (empty($errors)) {
        // Consulta a la base de datos o cualquier otra lógica para obtener los usuarios.
        // Esto se haría después de validar todos los filtros.
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../head.php'; ?>
    <style>
        /* Estilos para los errores de validación */
        .error {
            color: red;
            font-size: 0.9em;
        }

        .pagination {
            margin: 20px 0;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
        }

        .pagination .active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination a:hover {
            background-color: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:hover {
            background-color: #f9f9f9;
        }

        table td a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }

        table td a:hover {
            text-decoration: underline;
        }

        table td button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
        }

        table td button:hover {
            background-color: #c82333;
        }

        .search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .search-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .search-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .search-field label {
            margin-bottom: 5px;
            font-weight: 500;
        }

        .search-field input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-field button,
        .search-field a {
            margin-top: 10px;
            width: fit-content;
            align-self: flex-start;
        }

        .search-buttons {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .search-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .reset-btn {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-btn:hover,
        .reset-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../header.php'; ?>
    <?php require __DIR__ . '/../navigation.php'; ?>
    <main class="main">
        <h2>Gestión de Usuarios</h2>

        <!-- Formulario de búsqueda -->
        <form method="GET" class="search-form">
            <div class="search-grid">
                <!-- Fila 1 -->
                <div class="search-field">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($filters['nombre']); ?>">
                    <?php if (isset($errors['nombre'])): ?>
                        <span class="error"><?= $errors['nombre']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="search-field">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($filters['apellidos']); ?>">
                    <?php if (isset($errors['apellidos'])): ?>
                        <span class="error"><?= $errors['apellidos']; ?></span>
                    <?php endif; ?>
                </div>
                <!-- Fila 2 -->
                <div class="search-field">
                    <label for="nombre_usuario">Usuario:</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?= htmlspecialchars($filters['nombre_usuario']); ?>">
                    <?php if (isset($errors['nombre_usuario'])): ?>
                        <span class="error"><?= $errors['nombre_usuario']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="search-field">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($filters['email']); ?>">
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?= $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                <!-- Fila 3 -->
                <div class="search-field">
                    <label for="saldo_min">Saldo mínimo:</label>
                    <input type="text" id="saldo_min" name="saldo_min" value="<?= htmlspecialchars($filters['saldo_min']); ?>">
                    <?php if (isset($errors['saldo_min'])): ?>
                        <span class="error"><?= $errors['saldo_min']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="search-field">
                    <label for="saldo_max">Saldo máximo:</label>
                    <input type="text" id="saldo_max" name="saldo_max" value="<?= htmlspecialchars($filters['saldo_max']); ?>">
                    <?php if (isset($errors['saldo_max'])): ?>
                        <span class="error"><?= $errors['saldo_max']; ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="search-buttons">
                <button type="submit" class="search-btn">Buscar</button>
                <a href="/usuarios" class="reset-btn">Limpiar</a>
            </div>
        </form>

        <!-- Tabla de usuarios -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Nombre Usuario</th>
                    <th>Email</th>
                    <th>Fecha Nacimiento</th>
                    <th>Saldo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($errors)): ?>
                    <?php foreach ($data['usuarios'] as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']); ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?= htmlspecialchars($usuario['apellidos']); ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_usuario']); ?></td>
                            <td><?= htmlspecialchars($usuario['email']); ?></td>
                            <td><?= htmlspecialchars($usuario['fecha_nacimiento']); ?></td>
                            <td><?= htmlspecialchars($usuario['saldo']); ?></td>
                            <td>
                                <a href="/usuarios/editar?id=<?= $usuario['id']; ?>">Editar✏️</a>
                                <form method="POST" action="/usuarios/eliminar" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']); ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Borrar🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No se han encontrado resultados debido a los errores de validación.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($data['totalPages'] > 1): ?>
            <div class="pagination">
                <?php
                // Mantener filtros en la paginación
                $queryString = http_build_query($data['filters']);
                ?>
                <?php if ($data['currentPage'] > 1): ?>
                    <a href="?p=<?= $data['currentPage'] - 1 ?>&<?= $queryString ?>">← Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                    <?php if ($i == $data['currentPage']): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?p=<?= $i ?>&<?= $queryString ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($data['currentPage'] < $data['totalPages']): ?>
                    <a href="?p=<?= $data['currentPage'] + 1 ?>&<?= $queryString ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php require __DIR__ . '/../footer.php'; ?>
</body>

</html>