<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../head.php'; ?>
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
    </main>
    <?php require __DIR__ . '/../footer.php'; ?>
</body>

</html>