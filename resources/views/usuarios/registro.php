<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../head.php'; ?>
</head>

<body>
    <?php require __DIR__ . '/../header.php'; ?>
    <?php require __DIR__ . '/../navigation.php'; ?>
    <main class="main">
        <form action="/registro" method="POST" class="formulario">
            <h2>Registrar Nuevo Usuario</h2>

            <!-- Mostrar errores -->
            <?php if (!empty($data)): ?>
                <p style="color: red;"><?= htmlspecialchars($data['error']) ?></p>
            <?php endif; ?>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre">

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos">

            <label for="usuario">Nombre de usuario:</label>
            <input type="text" id="usuario" name="usuario">

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email">

            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña">

            <label for="saldo">Saldo inicial:</label>
            <input type="number" id="saldo" name="saldo" step="0.01">

            <button type="submit">Registrar</button>
        </form>
    </main>
    <?php require __DIR__ . '/../footer.php'; ?>
</body>

</html>