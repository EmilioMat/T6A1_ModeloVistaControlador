<main class="main">
    <?php
    // Verificar si hay un mensaje en la sesión
    if (isset($_SESSION['mensaje'])) {
        echo '<p>' . $_SESSION['mensaje'] . '</p>';

        // Limpiar el mensaje después de mostrarlo
        unset($_SESSION['mensaje']);
    }
    ?>

    <!-- Mostrar mensaje de bienvenida -->
    <?php if (!empty($mensaje)): ?>
        <p style="color: green;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>
</main>