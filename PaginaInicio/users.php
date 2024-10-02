<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != true) {
    echo '
    <script>
        alert("No eres administrador");
        window.location.href = "./inicio.php";
    </script>';
    exit;
}

require '../PHP/conexion.php';

$objConexion = new conexion();

// Manejo de la eliminación de usuarios
if ($_GET && isset($_GET['borrar'])) {
    $id = intval($_GET['borrar']);

    if ($id > 0) {
        $sql = "DELETE FROM `usuario` WHERE `id` = $id";
        if ($objConexion->ejecutarConsulta($sql) === false) {
            die('Error al eliminar el usuario: ' . $objConexion->getConexion()->error);
        } else {
            header("Location: users.php");
            exit;
        }
    } else {
        echo "ID no válido.";
    }
}

// Verificar si se ha enviado una búsqueda
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Modificar la consulta SQL
if ($busqueda != '') {
    $sql = "SELECT usuario.id, usuario.nombre, usuario.apellido, usuario.num_documento, informacion.PesoTotal, usuario.is_admin
            FROM usuario 
            LEFT JOIN informacion ON usuario.id = informacion.usuario_id 
            WHERE usuario.num_documento LIKE '%$busqueda%' 
            LIMIT 10";
} else {
    $sql = "SELECT usuario.id, usuario.nombre, usuario.apellido, usuario.num_documento, informacion.PesoTotal, usuario.is_admin
            FROM usuario 
            LEFT JOIN informacion ON usuario.id = informacion.usuario_id 
            ORDER BY usuario.is_admin DESC, usuario.fecha_creacion DESC
            LIMIT 10";
}

$result = $objConexion->ejecutarConsulta($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <link rel="stylesheet" href="users.css">
    <style>
        .admin-badge {
            color: white;
            background-color: #007bff;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 0.8rem;
        }
        .admin-icon {
            color: #007bff;
            margin-left: 5px;
        }
    </style>
    <script>
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                window.location.href = "users.php?borrar=" + id;
            }
        }
    </script>
</head>
<body>

    <h1>Lista de Usuarios</h1>

    <!-- Formulario de búsqueda -->
    <form method="GET" action="users.php">
        <input type="text" name="busqueda" placeholder="Buscar por número de documento" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit">Buscar</button>
        <a href="users.php">Limpiar búsqueda</a><a href="inicio.php">Volver</a>
    </form>

    <!-- Mostrar resultados de la búsqueda o lista de usuarios -->
    <?php
    if ($result && count($result) > 0) {
        echo "<div class='col-md-6'>";
        echo "<table class='table'>";
        echo "<thead>
                <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Num. Documento</th>
                <th>Peso total</th>
                <th>Acciones</th>
                </tr>
             </thead>";
        echo "<tbody>";

        foreach ($result as $row) {
            echo "<tr>";
            echo "<td>". htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row["nombre"]);

            // Si el usuario es administrador, mostrar la etiqueta y el ícono
            if ($row["is_admin"] == 1) {
                echo " <span class='admin-badge'>Administrador</span> <i class='admin-icon'>★</i>";
            }

            echo "</td>";
            echo "<td>" . htmlspecialchars($row["apellido"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["num_documento"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["PesoTotal"] ?? '0') . "kg" . "</td>";
            echo "<td><button onclick='confirmarEliminacion({$row['id']})'>Eliminar</button></td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "</div>";
    } else {
        echo "No se encontraron resultados.";
    }

    // Cerrar la conexión
    $objConexion->cerrarConexion();
    ?>

</body>
</html>
