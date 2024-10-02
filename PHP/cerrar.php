<?php

// Inicia la sesión si no está ya iniciada
session_start();

include 'conexion.php';

// Elimina todas las variables de sesión
$_SESSION = array();

// Finalmente, destruye la sesión
session_destroy();

// Redirige al usuario a la página de inicio o a donde prefieras
header("Location: ../programacion/sign-in.php");
exit();
?>
