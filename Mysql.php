<?php
class Mysql{

    private $server;
    private $database;
    private $username;
    private $password;
    private $conn;
    private $res;

    public $table;

    public function __construct($server, $database, $username, $password){

        $this->server = $server;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;

        $this->conn = mysqli_connect($this->server, $this->username, $this->password, $this->database);
        //check connection
        if (!$this->conn){
            die("Connection failed: " . mysqli_connect_error());
        }

        return;

    }

    //Ejecuta una consulta y guarda el resultado en $this->res
    private function get_res_query($query){

        $this->res = mysqli_query($this->conn, $query);

        return;

    }

    //Obtiene los nombres de las columnas del resultado almacenado en $this->res
    private function get_col_names(){

        $col_names = array();

        for($i=0; $i < $this->res->field_count; $i++){
            $field = $this->res->fetch_field();
            $col_names[] = $field;
        }

        return $col_names;

    }

    //Genera una consulta SELECT
    public function select($fields, $table, $where = "", $order = "", $limit = ""){

        //Guardamos el nombre de la tabla
        $this->table = $table;

        //Construimos la consulta

        //Si no se especifican campos, seleccionamos todos
        if($fields == []){
            $fields_str = "*";
        } else { 
            //Hay campos especificados
            $fields_str = implode(", ", $fields); //Convertimos el array de campos en una cadena separada por comas
        }

        $query = "SELECT " . $fields_str . " FROM " . $table;

        if($where != ""){
            $query .= " WHERE " . $where;
        }

        if($order != ""){
            $query .= " ORDER BY " . $order;
        }

        if($limit != ""){
            $query .= " LIMIT " . $limit;
        }

        echo "<br><br>Executing query: " . $query . "<br><br>";

        $this->get_table($query);

        return;

    }

    // Obtiene los campos de un registro basándose en los parámetros recibidos
    public function select_fields($params, $action){
        
        $table = ""; // Inicializar la variable de tabla

        // Construir la consulta SELECT
        $query = "SELECT * FROM ";

        // Inicializar la cláusula WHERE
        $where = "WHERE ";

        // Construir la cláusula WHERE basándose en los parámetros recibidos
        foreach ($params as $key => $value) {

            // Saltar el parámetro 'table'
            if ($key === 'table') {
                $table = $value; // Asignar el valor de la tabla
            }else{
                if ($where !== "WHERE ") {
                    $where .= " AND ";
                }
                $where .= $key . " = '" . $value . "'";
            }
          
        }       
        // Construir la consulta completa
        $query = $query . $table . " " . $where;
        // Ejecutar la consulta y mostrar la tabla de edición
        if($action == "edit"){
            $this->get_edit_table($query);
        }else if($action == "delete"){
            $this->get_delete_table($query);
        }
        return;

    }

    // Actualiza un registro basándose en los parámetros recibidos
    public function update($params){

        $table = isset($params['table']) ? $params['table'] : '';

        // Construir la consulta UPDATE
        $query = "UPDATE " . $table . " SET ";

        $set_clauses = [];
        $where_clauses = [];

        foreach ($params as $key => $value) {
            if ($key === 'table') {
                continue; // Saltar el parámetro 'table'
            }
            // Suponemos que el primer parámetro es la clave primaria para la cláusula WHERE
            if (empty($where_clauses)) {
                $where_clauses[] = $key . " = '" . $value . "'";
            } else {
                $set_clauses[] = $key . " = '" . $value . "'";
            }
        }

        $query .= implode(", ", $set_clauses);
        $query .= " WHERE " . implode(" AND ", $where_clauses);

        echo "<br><br>Executing update query: " . $query . "<br><br>";

        // Ejecutar la consulta UPDATE
        mysqli_query($this->conn, $query);

        // Redirigir de vuelta a la página principal
        header("Location: index.php");

        return;

    }
    
    // Elimina un registro basándose en los parámetros recibidos
    public function delete($params){

        $table = isset($params['table']) ? $params['table'] : '';

        // Construir la consulta DELETE
        $query = "DELETE FROM " . $table . " WHERE ";

        $where_clauses = [];

        foreach ($params as $key => $value) {
            if ($key === 'table') {
                continue; // Saltar el parámetro 'table'
            }
            $where_clauses[] = $key . " = '" . $value . "'";
        }

        $query .= implode(" AND ", $where_clauses);

        echo "<br><br>Executing delete query: " . $query . "<br><br>";

        // Ejecutar la consulta DELETE
        mysqli_query($this->conn, $query);

        // Redirigir de vuelta a la página principal
        header("Location: index.php");

        return;

    }

    //Genera una tabla HTML de edición a partir de una consulta
    public function get_edit_table($query){

        //Ejecutamos la Consulta
        $this->get_res_query($query);

        //Obtenemos el número de columnas
        $field_count = mysqli_num_fields($this->res);

        //Obtenemos los nombres de las columnas
        $col_names = $this->get_col_names();

        //Mostramos la tabla
        echo "<form method='post' action='update.php'>";
        echo "<table border='1'>";
        echo "<tr>";
        
        //Imprimimos los nombres de las columnas
        for($x=0; $x<count($col_names);$x++){
            echo "<th>" . $col_names[$x]->name . "</th>";
        }
        //Campo oculto con el nombre de la tabla
        echo "<input type='hidden' name='table' value='" . htmlspecialchars($this->table) . "'>";
        echo "</tr>";

        //Imprimimos los datos de la tabla
        while ($row = mysqli_fetch_array($this->res)) {
            echo "<tr>";
            for($x=0; $x<$field_count;$x++){
                echo "<td><input type='text' name='" . $col_names[$x]->name . "' value='" . $row[$x] . "'></td>";
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "<br><button type='submit'>Guardar Cambios</button>";
        echo "</form>";

        // Liberamos el resultado
        mysqli_free_result($this->res);

        // Cerramos la conexión
        mysqli_close($this->conn);

    }

    //Genera una tabla HTML de edición a partir de una consulta
    public function get_delete_table($query){

        //Ejecutamos la Consulta
        $this->get_res_query($query);

        //Obtenemos el número de columnas
        $field_count = mysqli_num_fields($this->res);

        //Obtenemos los nombres de las columnas
        $col_names = $this->get_col_names();

        //Mostramos la tabla
        echo "<form method='post' action='delete_register.php'>";
        echo "<table border='1'>";
        echo "<tr>";
        
        //Imprimimos los nombres de las columnas
        for($x=0; $x<count($col_names);$x++){
            echo "<th>" . $col_names[$x]->name . "</th>";
        }
        //Campo oculto con el nombre de la tabla
        echo "<input type='hidden' name='table' value='" . htmlspecialchars($this->table) . "'>";
        echo "</tr>";

        //Imprimimos los datos de la tabla
        while ($row = mysqli_fetch_array($this->res)) {
            echo "<tr>";
            for($x=0; $x<$field_count;$x++){
                echo "<td><input type='hidden' name='" . $col_names[$x]->name . "' value='" . $row[$x] . "'>".$row[$x]."</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "<br><button type='submit'>Eliminar Registro</button>";
        echo "</form>";

        // Liberamos el resultado
        mysqli_free_result($this->res);

        // Cerramos la conexión
        mysqli_close($this->conn);

    }



    //Genera una tabla HTML a partir de una consulta
    public function get_table($query){

        $id_field = FALSE; //Indica si el primer campo es un ID (para los enlaces de editar y eliminar)
        
        //Ejecutamos la Consulta
        $this->get_res_query($query);

        //Obtenemos el número de columnas
        $field_count = mysqli_num_fields($this->res);

        //Obtenemos el número de filas
        $rows_count = mysqli_num_rows($this->res);

        //Obtenemos los nombres de las columnas
        $col_names = $this->get_col_names();

        //Mostramos la tabla
        echo "<table border='1'>";
        echo "<tr>";
        
        //Imprimimos los nombres de las columnas
        for($x=0; $x<count($col_names);$x++){
            echo "<th>" . $col_names[$x]->name . "</th>";
            //Detectamos si existe el campo ID
            if(strtoupper($col_names[$x]->name) == "ID"){
                $id_field = TRUE;
            }
        }

        //Agregamos las columnas de Editar y Eliminar
        echo "<th>Editar</th>";
        echo "<th>Eliminar</th>";

        //Si existe el campo ID, lo indicamos
        //if($id_field){echo "id_field es TRUE<br>";} else {echo "id_field es FALSE<br>";}
      
        echo "</tr>";

        //Imprimimos los datos de la tabla
        while ($row = mysqli_fetch_array($this->res)) {
            echo "<tr>";
            for($x=0; $x<$field_count;$x++){
                echo "<td>" . $row[$x] . "</td>";
            }

            //Generamos los parámetros para las URLs de Editar y Eliminar
            $url_params = $this->build_url_params($col_names,$row,$id_field);

            echo "<td>";                
                    echo "<a href='edit.php?table=" . $this->table . "&" . $url_params . "'>✏️ Editar</a> ";                
            echo "</td>";
            echo "<td>";
                    echo "<a href='delete.php?table=" . $this->table . "&" . $url_params . "' onclick=\"return confirm('¿Eliminar este usuario?')\">🗑️ Eliminar</a>";
            echo "</td>";

            echo "</tr>";
        }

        echo "</table>";

        // Liberamos el resultado
        mysqli_free_result($this->res);

        // Cerramos la conexión
        mysqli_close($this->conn);

    }

    //Construye los parámetros para las URLs de Editar y Eliminar
    private function build_url_params($col_names, $row, $id_field){

        $url_params = "";

        if($id_field){
            //Si existe el campo ID, usamos solo ese campo para los parámetros
            for($x=0; $x<count($col_names);$x++){
                if(strtoupper($col_names[$x]->name) == "ID"){
                    $url_params .= "id=" . urlencode($row[$x]);
                    break;
                }
            }
        } else {
            //Si no existe el campo ID, usamos todos los campos para los parámetros
            for($x=0; $x<count($col_names);$x++){
                $url_params .= $col_names[$x]->name . "=" . urlencode($row[$x]);
                if($x < count($col_names)-1){
                    $url_params .= "&";
                }
            }
        }

        return $url_params;

    }


}
?>

