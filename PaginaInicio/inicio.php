<?php
session_start();
include '../PHP/conexion.php';

// Verificar si el usuario está conectado y si el ID de la sesión es numérico
if (!isset($_SESSION['usuario_id']) || !is_numeric($_SESSION['usuario_id'])) {
    die('No estás conectado. Verifica el ID de la sesión.');
}

// Obtener el ID del usuario de la sesión y si es administrador
$usuario_id = $_SESSION['usuario_id'];
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Crear una instancia de la clase conexion
$objConexion = new conexion();

// Verificar si el usuario es administrador
if (!$is_admin) {
    die('Acceso denegado: Solo los administradores pueden acceder a esta página.');
}

$usuario_encontrado = [];
$dato3_value = 100; // Valor por defecto de dato3

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['num_documento']) && empty($_POST['usuario_id'])) {
        // Buscar por número de documento
        $num_documento_buscar = $_POST['num_documento'];
        $consulta_usuario = "SELECT * FROM usuario WHERE num_documento = '$num_documento_buscar'";
        $usuario_encontrado = $objConexion->ejecutarConsulta($consulta_usuario);
        if (empty($usuario_encontrado)) {
            $error_documento = "<script>alert('Usuario no encontrado, por favor inténtelo de nuevo');</script>";
        }
    } elseif (isset($_POST['usuario_id'])) {
        // Actualizar o insertar información
        $usuario_id_seleccionado = intval($_POST['usuario_id']);
        $peso_total = $_POST['PesoTotal'];
        $nombre_residuo = $_POST['NombreResiduo'];
        
        // Convertir peso_total a numérico
        $peso_total_numeric = is_numeric($peso_total) ? floatval($peso_total) : 0;

        // Consultar la información actual del usuario
        $consulta_verificar = "SELECT * FROM informacion WHERE usuario_id = $usuario_id_seleccionado LIMIT 1";
        $resultado = $objConexion->getConexion()->query($consulta_verificar);

        if ($resultado->num_rows > 0) {
            // Actualizar información existente
            $fila = $resultado->fetch_assoc();
            $nombre_residuos_actual_json = $fila['NombreResiduos'];
            $nombre_residuos_actual_array = json_decode($nombre_residuos_actual_json, true);
            $dato3_actual = $fila['dato3']; // Obtener el valor actual de dato3

            // Verificar si el tope (dato3) es 0 o menor antes de proceder
            if ($dato3_actual <= 0) {
                echo "<script>alert('No se puede ingresar más información, el tope (KG) ha llegado a 0.');</script>";
            } elseif ($peso_total_numeric > $dato3_actual) {
                // Verificar si el peso total que intenta ingresar es mayor que el tope actual
                echo "<script>alert('El peso ingresado excede el tope disponible. No puedes ingresar más de $dato3_actual KG.');</script>";
            }
            else {
                // Agregar nuevo residuo si no está ya presente
                if (!in_array($nombre_residuo, $nombre_residuos_actual_array)) {
                    $nombre_residuos_actual_array[] = $nombre_residuo;
                }
                $nombre_residuos_actualizado_json = json_encode($nombre_residuos_actual_array);

                // Calcular el nuevo dato3 restando PesoTotal del dato3 anterior
                $nuevo_dato3 = $dato3_actual - $peso_total_numeric;

                // Si el nuevo dato3 es menor a 0, ajustarlo a 0 y evitar más inserciones
                if ($nuevo_dato3 < 0) {
                    $nuevo_dato3 = 0;
                }

                // Actualizar la base de datos
                $consulta_actualizar = "
                    UPDATE informacion 
                    SET PesoTotal = PesoTotal + $peso_total_numeric, 
                        NombreResiduos = '$nombre_residuos_actualizado_json', 
                        dato3 = '$nuevo_dato3', 
                        fecha = NOW() 
                    WHERE usuario_id = $usuario_id_seleccionado
                ";
                $objConexion->getConexion()->query($consulta_actualizar);
                echo "<script>alert('Información actualizada exitosamente');</script>";

                // Asignar el nuevo valor de dato3 para mostrar en el formulario
                $dato3_value = $nuevo_dato3;
            }
        } else {
            // Insertar nueva información
            $nombre_residuos_json = json_encode([$nombre_residuo]);
            $nuevo_dato3 = 100 - $peso_total_numeric; // Para nueva información, restar directamente de 100

            // Verificar si el nuevo dato3 es 0 o menor
            if ($nuevo_dato3 < 0) {
                $nuevo_dato3 = 0; // Ajustar a 0
            }

            $consulta_insertar = "
                INSERT INTO informacion (PesoTotal, NombreResiduos, dato3, usuario_id, fecha) 
                VALUES ('$peso_total_numeric', '$nombre_residuos_json', '$nuevo_dato3', $usuario_id_seleccionado, NOW())
            ";
            $objConexion->getConexion()->query($consulta_insertar);
            echo "<script>alert('Información insertada exitosamente.');</script>";

            // Asignar el nuevo valor de dato3
            $dato3_value = $nuevo_dato3;
        }
    }
}

// Consulta para obtener el valor actual de dato3 después de buscar un usuario
if (!empty($usuario_encontrado)) {
    $usuario_id_encontrado = $usuario_encontrado[0]['id'];
    $consulta_dato3 = "SELECT dato3 FROM informacion WHERE usuario_id = $usuario_id_encontrado LIMIT 1";
    $resultado_dato3 = $objConexion->getConexion()->query($consulta_dato3);
    
    if ($resultado_dato3->num_rows > 0) {
        $fila_dato3 = $resultado_dato3->fetch_assoc();
        $dato3_value = $fila_dato3['dato3']; // Actualizar con el valor de la base de datos
    }
}


// Consulta para obtener todos los usuarios registrados
$consulta_usuarios = "SELECT * FROM usuario";
$usuarios = $objConexion->ejecutarConsulta($consulta_usuarios);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CorpoAyapel</title>
    <link rel="stylesheet" href="inicio.css">
    <link rel="icon" type="image/x-icon" href="logo.svg">
    <link href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css" rel="stylesheet">
</head>

<body>
    <div class="encabezado">
        <?php if (!empty($usuarios)) { ?>
            <?php
            $usuario = $usuarios[0]; // Obtener el primer (y único) resultado
            $primer_nombre = explode(' ', $usuario['nombre'])[0];
            $primer_apellido = explode(' ', $usuario['apellido'])[0];
            ?>
            <h1>Bienvenido <?php echo htmlspecialchars($primer_nombre) . ' ' . htmlspecialchars($primer_apellido); ?></h1>
        <?php } else { ?>
            <h1>No se encontró el usuario</h1>
        <?php } ?>
        <div class="lado">
            <?php if ($is_admin == 1) { ?>
                <h5>Administrador</h5>
            <?php } else { ?>
                <i class="fi fi-rr-user"></i>
                <h5>Usuario</h5>
            <?php } ?>
            <img src="logo.svg" alt="" class="logo">
        </div>
    </div>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <ul>
                <li><a href="users.php">Ver usuarios registrados</a></li>
                <li><a href="#historial">Ver Historial Global</a></li>
                <li><a href="reportes.php">Descargar Reportes</a></li>
                <?php if ($is_admin == 1) { ?>
                    <li><a href="../formulario/admin-sign-in.php">Registrar administrador</a></li>
                <?php } ?>
            </ul>
            <div class="cerrar">
                <form action="../PHP/cerrar.php" method="post">
                    <button type="submit" class="boton">Cerrar sesión</button>
                </form>
            </div>
        </div>
        <div class="main">
            <h2>CorpoAyapel</h2>
            <div class="parents">
                <h3 class="texto">Entidad sin ánimo de lucro que promueve el desarrollo sostenible del complejo cenagoso de Ayapel y de su comunidad.</h3>
            </div>
            <h4>Insertar Información del Usuario</h4>
            <form action="inicio.php" method="post">
                <?php if (empty($usuario_encontrado)) { ?>
                    <div class="seleccion">
                        <label for="num_documento" style="font-weight: bold;">Buscar por número de documento:</label>
                        <input type="text" name="num_documento" id="num_documento" placeholder="Número de documento" required>
                        <button type="submit">Buscar</button>
                    </div>
                    <?php if (!empty($error_documento)) {
                        echo $error_documento;
                    } ?>
                <?php } else { ?>
                    <div class="seleccion">
                        <label for="usuario_id" style="font-weight: bold;">Usuario:</label>
                        <select name="usuario_id" id="usuario_id" style="appearance: none; padding:5px;">
                            <option value="<?php echo htmlspecialchars($usuario_encontrado[0]['id']); ?>">
                                <?php echo htmlspecialchars(explode(' ', $usuario_encontrado[0]['nombre'])[0]) . ' ' . htmlspecialchars(explode(' ', $usuario_encontrado[0]['apellido'])[0]); ?>
                            </option>
                        </select>
                        <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario_encontrado[0]['id']); ?>">
                    </div>
                    <div class="dato">
                        <div class="dato1 datos">
                            <label for="peso" class="form">Peso Total</label><br>
                            <input type="number" name="PesoTotal" required style="width: 90%;">
                        </div>
                        <div class="dato2 datos">
                            <label for="nombre_residuo" class="form">Nombre del Residuo</label><br>
                            <input type="text" name="NombreResiduo" required style="width: 90%;">
                        </div>
                        <div class="dato3 datos">
                            <label for="dato3" class="form">Tope de Residuo (100KG)</label><br>
                            <input type="text" name="dato3" value="<?php echo htmlspecialchars($dato3_value); ?>" readonly style="width: 90%;"><br>
                        </div>
                    </div>
                    <div class="enviar">
                        <button type="submit">Insertar/Actualizar</button>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>
    <div class="footer">
            <p>Todos los derechos reservados</p>
        </div>
       
    <script src="inicio.js"></script>
</body>

</html>
