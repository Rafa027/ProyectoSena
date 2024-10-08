<?php
session_start();
include '../PHP/conexion.php'; // Asegúrate de que esta ruta es correcta
require '../vendor/autoload.php'; // Asegúrate de que PHPMailer esté instalado

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $objConexion = new conexion();

    // Verifica que el email existe en la tabla de usuarios
    $consulta = "SELECT * FROM usuario WHERE correo = '$email'";
    $resultado = $objConexion->getConexion()->query($consulta);

    if ($resultado->num_rows > 0) {
        // Generar token único
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Expiración en 1 hora

        // Guardar el token en la tabla password_resets
        $consulta_insertar = "INSERT INTO password_resets (email, token, expiry) VALUES ('$email', '$token', '$expiry')";
        if ($objConexion->getConexion()->query($consulta_insertar) === TRUE) {
            // Preparar el correo
            $reset_link = "http://localhost/P1.1/Proyecto/programacion/reset_password.php?token=$token"; // Cambia esto según tu configuración
            $mail = new PHPMailer(true);

            try {
                // Configura el servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Cambia esto a tu servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'rafaelopezavila2006@gmail.com'; // Cambia esto
                $mail->Password = 'jyre pszs scra bnmn'; // Cambia esto o usa App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // o PHPMailer::ENCRYPTION_SMTPS si es necesario
                $mail->Port = 587; // Cambia esto si es necesario

                // Configura el correo
                $mail->setFrom('rafaelopezavila2006@gmail.com', 'CorpoAyapel');
                $mail->addAddress($email);

                // Establecer el charset a UTF-8
                $mail->CharSet = 'UTF-8';

                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body = "Haz clic en el siguiente enlace para restablecer tu contraseña: $reset_link";

                $mail->send();
                echo 'Se ha enviado un enlace de restablecimiento de contraseña a tu correo electrónico.';
            } catch (Exception $e) {
                echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error al guardar el token en la base de datos.";
        }
    } else {
        echo "No se encontró el correo electrónico en nuestra base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvidé mi contraseña</title>
</head>

<body>
    <h2>Recuperar Contraseña</h2>
    <form action="olvidar_contrasena.php" method="post">
        <input type="email" name="email" placeholder="Tu correo electrónico" required>
        <button type="submit">Enviar enlace de recuperación</button>
    </form>
</body>

</html>