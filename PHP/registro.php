<?php

if ($_POST) {
    include 'conexion.php'; // Asegúrate de que el archivo conexion.php esté en la ruta correcta

    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $t_documento = $_POST['Tdocumento'];
    $num_documento = $_POST['Ndocumento'];
    $correo = $_POST['correo'];
    $num_contacto = $_POST['contacto']; // Corrige el nombre del campo
    $password = $_POST['password'];
    $password = hash('sha512', $password);
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
                window.location.href = "../formulario/formulario registro.php";
            </script>
        ';
    }
    
    else if(mysqli_num_rows($verificar_cedula) > 0){
        echo '
            <script>
                alert("Numero de documento existente");
                window.location.href = "../formulario/formulario registro.php";
            </script>
        ';
    }

    else if(mysqli_num_rows($verificar_contacto) > 0){
        echo '
            <script>
                alert("Contacto existente");
                window.location.href = "../formulario/formulario registro.php";
            </script>
        ';
    }
    
    else {
        // Preparar y ejecutar la inserción de datos
        $sql = "INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `t_documento`, `num_documento`, `correo`, `num_contacto`, `contrasena`) VALUES (NULL, '$nombre', '$apellido', '$t_documento', '$num_documento', '$correo', '$num_contacto', '$password')";

        if (mysqli_query($conexion, $sql)) {
            echo '
                <script>
                    alert("Usuario registrado exitosamente");
                    window.location.href = "../formulario/formulario registro.php";
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
}
?>