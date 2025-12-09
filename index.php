<?php
    // Include connection constants
    include 'connection.php';
    // Include Mysql class
    include 'Mysql.php';

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
    <?php
        // Create Mysql object 
        $obj_mysql = new Mysql(SERVER, DATABASE, USERNAME, PASSWORD);
        // Use select to build a proper SELECT query and display the table
        //$obj_mysql->select([], 'city', 'CountryCode = "ESP"', 'Population DESC', '10');
        $obj_mysql->select(["ID","Name", "District", "Population"], 'city', 'CountryCode = "ESP"', 'Population DESC', '10');    
        //$obj_mysql->select(["CountryCode", "Language", "IsOfficial", "Percentage"], 'countrylanguage', 'CountryCode = "ESP"', 'Percentage DESC', '');
    ?>
</body>
</html>