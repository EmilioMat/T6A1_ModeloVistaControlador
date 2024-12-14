<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../head.php'; ?>
</head>

<body>
    <?php require __DIR__ . '/../header.php'; ?>
    <?php require __DIR__ . '/../navigation.php'; ?>
    <main class="main">
        <form action="/login" method="POST" class="formulario">
            <h2>Iniciar Sesión</h2>

            <?php if (!empty($data['errores'])): ?>
                <?php foreach ($data['errores'] as $error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            <?php endif; ?>

            <label for="usuario">Nombre de usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Introduce tu usuario">

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" placeholder="Introduce tu contraseña">

            <button type="submit" class="btn-enviar">Iniciar sesión</button>
        </form>

        <!-- Enlace para redirigir al formulario de registro -->
        <p>¿No tienes cuenta? <a href="/registro">Regístrate aquí</a></p>
    </main>
    <?php require __DIR__ . '/../footer.php'; ?>
</body>

</html>