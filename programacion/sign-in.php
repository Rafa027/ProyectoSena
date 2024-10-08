<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear usuario</title>
    <link rel="icon" href="../main/imagenes/corpo-icon.png" type="image/png">
    <link rel="stylesheet" href="sign-in.css">
    <link rel="stylesheet" href="../main/fonts.css">
</head>
<body>
    <div class="sign-in-one">

        <a href="../index.html" target="_blank" class="imagen"><img src="../imagenes/logo-prueba.png"></a>

        <form action="sign-in.php" method="post">

            <h2>Iniciar Sesión</h2>

            <input type="text" placeholder="Ingresa tu Cédula" name="Ndocumento">
            <input type="password" placeholder="Ingresa tu Contraseña" name="password">
            <p class="error"></p>
            
            <button type="submit" class="session">Iniciar Sesion</button>
            <a href="olvidar_contrasena.php" class="contra">¿Olvidaste tu Contraseña?</a>
            <div class="link">
            <a href="../formulario/formulario registro.php" class="account">Crear Cuenta</a>
            <a href="../index.html">Volver a inicio</a>
        </div>

        </form>
    </div>
</body>
</html>
<?php
session_start();
if($_POST){
include '../PHP/conexion.php';
$num_documento = $_POST['Ndocumento'];
$password = $_POST['password'];
$password = hash('sha512', $password);

$objConexion = new conexion();
$conexion = $objConexion->getConexion();

$validar_login = mysqli_query($conexion, "SELECT * FROM usuario WHERE num_documento='$num_documento' and contrasena='$password'");

if (mysqli_num_rows($validar_login) > 0) {
    $usuario = mysqli_fetch_assoc($validar_login);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['is_admin'] = $usuario['is_admin']; // Guardar si es administrador
    echo 'ID de usuario guardado en sesión: ' . htmlspecialchars($_SESSION['usuario_id']) . '<br>';
    if ($_SESSION['is_admin'] == 1) {
            // Si es administrador, redirigir al panel de administración
            header("Location: ../PaginaInicio/inicio.php");
    } else {
            // Si es usuario regular, redirigir a la página de inicio regular
            header("Location: ../PaginaInicio/inicio.php");
    }
}
else{
    echo '
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const errorContainer = document.querySelector(".error");

            // Limpiar cualquier mensaje de error anterior
            errorContainer.innerHTML = "";

            errorContainer.textContent = "Usuario o Contraseña incorrectas";

        });
    </script>
    ';
}
$objConexion->cerrarConexion();
}
?>