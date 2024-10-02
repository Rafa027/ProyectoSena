<?php
session_start();
include '../PHP/conexion.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die('Acceso denegado: Solo los administradores pueden acceder a esta página.');
}

// Cargar la biblioteca PhpSpreadsheet
require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objConexion = new conexion();

// Verificar si se ha enviado el formulario de generación de reporte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtén el mes y el año de los inputs
    $mes = intval($_POST['mes']);
    $anio = intval($_POST['anio']);

    // Consulta para obtener los registros de la tabla 'informacion' del mes y año seleccionados
    $consulta = "
        SELECT usuario_id, PesoTotal, fecha
        FROM informacion
        WHERE MONTH(fecha) = $mes AND YEAR(fecha) = $anio
    ";

    $resultado = $objConexion->getConexion()->query($consulta);

    if ($resultado->num_rows > 0) {
        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte Mensual');

        // Encabezados
        $sheet->setCellValue('A1', 'ID Usuario');
        $sheet->setCellValue('B1', 'Peso Total');
        $sheet->setCellValue('C1', 'Fecha');

        // Rellenar los datos y calcular la suma total
        $fila_num = 2; // Comenzamos en la fila 2 para evitar los encabezados
        $suma_total = 0;
        
        while ($fila = $resultado->fetch_assoc()) {
            $sheet->setCellValue('A' . $fila_num, $fila['usuario_id']);
            $sheet->setCellValue('B' . $fila_num, $fila['PesoTotal']);
            $sheet->setCellValue('C' . $fila_num, $fila['fecha']);
            
            // Sumar el PesoTotal al total general
            $suma_total += $fila['PesoTotal'];
            $fila_num++;
        }

        // Agregar el total al final del archivo
        $sheet->setCellValue('A' . $fila_num, 'Total');
        $sheet->setCellValue('B' . $fila_num, $suma_total);

        // Generar el archivo XLSX
        $writer = new Xlsx($spreadsheet);
        $filename = "reporte_$mes-$anio.xlsx";

        // Enviar las cabeceras adecuadas para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        // Guardar el archivo y enviarlo al navegador para su descarga
        $writer->save('php://output');
        exit();
    } else {
        echo "<script>
                alert('No se encuentran datos para esta fecha');
            </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Reporte</title>
    <link rel="stylesheet" href="reportes.css">
</head>
<body>
    
    <h1>Generar Reporte Mensual</h1>
    <div class="container">
    <form action="reportes.php" method="post">
        <div class="centrado">
        <label for="mes">Mes (1-12):</label>
        <input type="number" name="mes" id="mes" min="1" max="12" required><br>
        <label for="anio">Año:</label>
        <input type="number" name="anio" id="anio" min="2024" required><br>
        <button type="submit">Generar Reporte</button>
        </div>
    </form>
    </div>
</body>
</html>
