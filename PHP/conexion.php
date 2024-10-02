<?php

class conexion {
    private $servidor = 'localhost'; // Cambia según tu configuración
    private $usuario = 'root'; // Cambia según tu configuración
    private $contraseña = 'Rlopez2006'; // Cambia según tu configuración
    private $db = 'login-register'; // Cambia según tu configuración
    private $conexion;

    // Constructor para establecer la conexión
    public function __construct() {
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->contraseña, $this->db);

        // Verificar si hubo un error en la conexión
        if ($this->conexion->connect_error) {
            die('Error de conexión: ' . $this->conexion->connect_error);
        }
    }

    // Método para obtener la conexión
    public function getConexion() {
        return $this->conexion;
    }

    // Método para cerrar la conexión
    public function cerrarConexion() {
        $this->conexion->close();
    }

    // Método para ejecutar una consulta
    public function ejecutarConsulta($consulta) {
        $resultado = $this->conexion->query($consulta);
        
        if ($resultado) {
            if ($resultado->num_rows > 0) {
                return $resultado->fetch_all(MYSQLI_ASSOC); // Devolver todos los resultados como un array asociativo
            } else {
                return []; // No hay resultados
            }
        } else {
            die('Error en la consulta: ' . $this->conexion->error);
        }
    }
}
