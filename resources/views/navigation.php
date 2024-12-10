<nav class="nav">
    <a href="/" class="nav__link">
        <p class="nav__text">Inicio</p>
    </a>
    <a href="/usuario/crear-base" class="nav__link">
        <p class="nav__text">Crear base de datos</p>
    </a>
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <!-- El usuario está autenticado, mostramos el enlace al panel -->
        <a href="/usuario/<?php echo $_SESSION['usuario_id']; ?>" class="nav__link">
            <p class="nav__text">Panel</p>
        </a>

        <!-- Enlace para cerrar sesión -->
        <a href="/logout" class="nav__link" id="logout-link">
            <p class="nav__text">Cerrar Sesión</p>
        </a>
    <?php else: ?>
        <!-- El usuario no está autenticado, mostramos el enlace para iniciar sesión -->
        <a href="/login" class="nav__link">
            <p class="nav__text">Iniciar Sesión</p>
        </a>
    <?php endif; ?>
</nav>