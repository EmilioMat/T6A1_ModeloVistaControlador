<?php
session_destroy();

// Redirigir al usuario a la página principal
header("Location: /");
exit();