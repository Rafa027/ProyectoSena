<?php
session_start();
include '../PHP/conexion.php'; // Asegúrate de incluir tu archivo de conexión

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $objConexion = new conexion();

    // Verifica si el token es válido y no ha expirado
    $consulta = "SELECT email FROM password_resets WHERE token = '$token' AND expiry > NOW()";
    $resultado = $objConexion->getConexion()->query($consulta);

    if ($resultado->num_rows > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nueva_contrasena = hash('sha512', ($_POST['nueva_contrasena']));

            // Obtiene el email del token
            $fila = $resultado->fetch_assoc();
            $email = $fila['email'];

            // Actualiza la contraseña en la base de datos
            $actualizar = "UPDATE usuario SET contrasena = '$nueva_contrasena' WHERE correo = '$email'";
            $objConexion->getConexion()->query($actualizar);

            // Elimina el token de la base de datos
            $eliminar_token = "DELETE FROM password_resets WHERE token = '$token'";
            $objConexion->getConexion()->query($eliminar_token);

            echo "Su contraseña ha sido restablecida con éxito.";
        }
    } else {
        echo "Este enlace de restablecimiento de contraseña no es válido o ha expirado.";
    }
} else {
    echo "Token no proporcionado.";
}
?>

<form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="post">
    <label for="nueva_contrasena">Ingrese su nueva contraseña:</label>
    <input type="password" name="nueva_contrasena" required>
    <button type="submit">Restablecer contraseña</button>
</form>
