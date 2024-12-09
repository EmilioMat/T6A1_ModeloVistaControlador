<!DOCTYPE html>
<html lang="en">

<head>
    <?php require 'head.php'; ?>
</head>

<body>
    <?php require 'header.php'; ?>
    <?php require 'navigation.php'; ?>
    <main class="main">
        <form action="/login" method="POST" class="formulario">
            <h2>Iniciar Sesión</h2>

            <!-- Mostrar errores -->
            <?php if (!empty($data)): ?>
                <p style="color: red;"><?= htmlspecialchars($data['error']) ?></p>
            <?php endif; ?>

            <label for="usuario">Nombre de usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Introduce tu usuario">

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" placeholder="Introduce tu contraseña">

            <button type="submit" class="btn-enviar">Iniciar sesión</button>
        </form>
    </main>
    <?php require 'footer.php'; ?>
</body>

</html>