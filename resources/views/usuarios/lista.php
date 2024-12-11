<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/../head.php'; ?>
    <style>
        .pagination {
            margin: 20px 0;
            text-align: center;
        }
        .pagination a, .pagination span {
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

        /* Estilo para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
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
    </style>
</head>
<body>
    <?php require __DIR__ . '/../header.php'; ?>
    <?php require __DIR__ . '/../navigation.php'; ?>
    <main class="main">
        <h2>Gestión de Usuarios</h2>

        <!-- Tabla de usuarios -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Nombre Usuario</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['usuarios'] as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']); ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?= htmlspecialchars($usuario['apellidos']); ?></td>
                        <td><?= htmlspecialchars($usuario['nombre_usuario']); ?></td>
                        <td><?= htmlspecialchars($usuario['email']); ?></td>
                        <td>
                            <a href="/lista/editar?id=<?php echo $usuario['id']; ?>">Editar✏️</a>
                            <form method="POST" action="/lista/eliminar" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']); ?>">
                                <button type="submit" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Borrar🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Controles de paginación -->
        <?php if ($data['totalPages'] > 1): ?>
            <div class="pagination">
                <?php if ($data['currentPage'] > 1): ?>
                    <a href="?p=<?= $data['currentPage'] - 1 ?>">← Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                    <?php if ($i == $data['currentPage']): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?p=<?= $i ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($data['currentPage'] < $data['totalPages']): ?>
                    <a href="?p=<?= $data['currentPage'] + 1 ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php require __DIR__ . '/../footer.php'; ?>
</body>
</html>
