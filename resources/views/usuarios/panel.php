<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../head.php'; ?>
</head>

<body>
    <?php require __DIR__ . '/../header.php'; ?>
    <?php require __DIR__ . '/../navigation.php'; ?>
    <main class="main">
        <form method="POST" action="/usuario/editar" class="formulario">
            
            <!-- Mostrar errores globales -->
            <?php if (!empty($data['errores'])): ?>
                <ul style="color: red;">
                    <?php foreach ($data['errores'] as $campo => $mensaje): ?>
                        <li><?= htmlspecialchars($mensaje); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <input type="hidden" name="id" value="<?php echo $data['usuario']['id']; ?>">

            <label for="nombre">Nombre: <?php echo $data['usuario']['nombre']; ?></label>
            <input type="text" name="nombre" id="nombre">

            <label for="apellidos">Apellidos: <?php echo $data['usuario']['apellidos']; ?></label>
            <input type="text" name="apellidos" id="apellidos">

            <label for="usuario">Nombre de Usuario: <?php echo $data['usuario']['nombre_usuario']; ?></label>
            <input type="text" name="usuario" id="usuario">

            <label for="email">Email: <?php echo $data['usuario']['email']; ?></label>
            <input type="email" name="email" id="email">

            <label for="fecha_nacimiento">Fecha de Nacimiento: <?php echo $data['usuario']['fecha_nacimiento']; ?></label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">

            <button type="submit" class="btn-enviar">Actualizar Datos</button>
        </form>
    </main>
    <?php require __DIR__ . '/../footer.php'; ?>
</body>

</html>