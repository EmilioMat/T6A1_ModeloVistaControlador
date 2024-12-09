<?php
session_start();
?>

<main class="main">
    <p class="header-paragraph">Página principal</p>

    <?php
    // Verificar si hay un mensaje en la sesión
    if (isset($_SESSION['mensaje'])) {
        echo '<p>' . $_SESSION['mensaje'] . '</p>';

        // Limpiar el mensaje de la sesión después de mostrarlo
        unset($_SESSION['mensaje']);
    }
    ?>
</main>