<?php
    // Include connection constants
    include 'connection.php';
    // Include Mysql class
    include 'Mysql.php';

// Obtener el nombre de la tabla desde los parámetros de la solicitud
$tabla = isset($_REQUEST['table']) ? $_REQUEST['table'] : '';

// Obtener todos los parámetros de la solicitud (GET y POST)
$params = $_REQUEST;

// Create Mysql object 
$obj_mysql = new Mysql(SERVER, DATABASE, USERNAME, PASSWORD);

// Asignar el nombre de la tabla al objeto Mysql
$obj_mysql->table = $tabla;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD PHP + MySQL</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
    <h1>CRUD PHP + MySQL</h1>
    <h2>Parámetros recibidos:</h2>
    <?php
    // Mostrar los parámetros recibidos
    echo "<h3>Tabla: " . $obj_mysql->table . "</h3>";
    // Llamar al método para mostrar los campos de edición
    $obj_mysql->select_fields($params,"delete");

  


?>
</body>
</html>





