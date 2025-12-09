<?php
    // Include connection constants
    include 'connection.php';
    // Include Mysql class
    include 'Mysql.php';
    $mysql = new Mysql(SERVER, DATABASE, USERNAME, PASSWORD);    

    // Obtener todos los parámetros de la solicitud (GET y POST)
    $params = $_REQUEST;

    // Llamar al método para actualizar el registro
    $mysql->update($params);

    //phpinfo();

?>