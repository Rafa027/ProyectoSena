<?php

session_start();

// Verificar si el usuario es un administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != false) {
    // Si no es administrador, mostrar mensaje de error
    echo '
    <script>
        alert("Regitrar a un administrador, significara darles privilegios de adminitrador");
    </script>
    ';
}

require '../PHP/conexion.php';

if ($_POST) {
    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $t_documento = $_POST['Tdocumento'];
    $num_documento = $_POST['Ndocumento'];
    $correo = $_POST['correo'];
    $num_contacto = $_POST['contacto']; // Corrige el nombre del campo
    $password = $_POST['password'];
    $password = hash('sha512', $password);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0; // Verificar si el checkbox fue marcado
    // Crear una instancia de la clase de conexión
    $objConexion = new conexion();
    $conexion = $objConexion->getConexion(); // Asegúrate de que este método devuelva una conexión válida

    // Verificar si el correo ya existe
    $verificar_correo = mysqli_query($conexion, "SELECT * FROM usuario WHERE correo='$correo'");
    $verificar_cedula = mysqli_query($conexion, "SELECT * FROM usuario WHERE num_documento='$num_documento'");
    $verificar_contacto = mysqli_query($conexion, "SELECT * FROM usuario WHERE num_contacto='$num_contacto'");
    if (mysqli_num_rows($verificar_correo) > 0) {
        echo '
            <script>
                alert("Correo existente");
                header(Location: "admin-sign-in.php");
            </script>
        ';
    }
    
    else if(mysqli_num_rows($verificar_cedula) > 0){
        echo '
            <script>
                alert("Numero de documento existente");
                window.location.href = "./admin-sign-in.php";
            </script>
        ';
    }

    else if(mysqli_num_rows($verificar_contacto) > 0){
        echo '
            <script>
                alert("Contacto existente");
                window.location.href = "./admin-sign-in.php";
            </script>
        ';
    }
    
    else {
        // Preparar y ejecutar la inserción de datos
        $sql = "INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `t_documento`, `num_documento`, `correo`, `num_contacto`, `contrasena`, `is_admin`) VALUES (NULL, '$nombre', '$apellido', '$t_documento', '$num_documento', '$correo', '$num_contacto', '$password', '$is_admin')";

        if (mysqli_query($conexion, $sql)) {
            echo '
                <script>
                    alert("Administrador registrado exitosamente");
                    header(Location: "admin-sign-in.php");
                </script>
            ';
        } else {
            echo '
                <script>
                    alert("Error al registrar usuario: ' . mysqli_error($conexion) . '");
                </script>
            ';
        }
    }

    // Cerrar la conexión
    $objConexion->cerrarConexion(); // Asegúrate de que este método esté implementado en la clase de conexión
};
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regristo de usuario</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <H1>REGISTRE SUS DATOS</H1>
    <form action="admin-sign-in.php" method="post">
        <div class="encabezado">
            <img src="logo-prueba.png" alt="logo" width="100px"><br><br>
        </div>
        <label for="username" class="label">NOMBRES</label>
        <input type="text" id="Nombres" placeholder="Nombres completos" name="nombre" required>
        <label for="username" class="label">APELLIDOS</label>
        <input type="text" id="Apellidos" placeholder="Apellidos" name="apellido" required>
        <label for="username" class="label">TIPO DE DOCUMENTO</label>
        <select name="Tdocumento" id="" class="TD" required>
            <option value="">Seleccione su documento</option>
            <option value="Cedula de ciudadania">Cedula de ciudadania</option>
            <option value="Tarjeta de Identidad">Tarjeta de identidad</option>
            <option value="Cedula extranjera">Cedula Extranjera</option>
        </select>
        <label for="username" class="label">DOCUMENTO</label>
        <input type="text" id="documento" placeholder="Numero de documento" class="container" name="Ndocumento" required>
        <label for="username" class="label">CORREO ELECTRONICO</label>
        <input type="email" name="correo" id="" required>
        <label for="username" class="label">TELEFONO</label>
        <input type="number" id="Telefono" placeholder="# celular" class="numero" name="contacto" required>
        <label for="password" class="label">CONTRASEÑA</label>
        <input type="password" id="Contraseña" placeholder="contraseña" class="password" name="password" required><br><br>
        <label class="custom-checkbox">
    <input type="checkbox" id="admin-checkbox">
    <span class="checkmark"></span>
    Activar modo administrador
</label>
        <button type="submit">Crear Administador</button><br><br>
    </form>
    <script src="index.js"></script>
</body>

</html>