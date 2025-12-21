<?php
    // Include connection constants
    include 'connection.php';


        $server = SERVER;
        $database = DATABASE;
        $username = USERNAME;
        $password = PASSWORD;

        $conn = mysqli_connect(SERVER, USERNAME, PASSWORD, DATABASE);
        //check connection
        if (!$conn){
            die("Connection failed: " . mysqli_connect_error());
        }
/*
        //query to get country ID and Code
        $res = mysqli_query($conn, "SELECT ID, Code FROM country");

        //create array to hold ID and Code
        $table_country = [];

        //fetch data and populate array
        while ($row = mysqli_fetch_assoc($res)) {

            //assign ID as key and Code as value
            $table_country[$row['ID']] = $row['Code'];

        }
*/
        //query to get country ID and Code
        $res = mysqli_query($conn, "SELECT * FROM city");

        //create array to hold ID and Code 
        while ($row = mysqli_fetch_assoc($res)) {

            //assign ID as key and Name as value
            $id = $row['ID'];
            $countrycode = $row['CountryCode'];

            $res_country = mysqli_query($conn, "SELECT ID FROM country WHERE Code = '$countrycode'");
            $row_country = mysqli_fetch_assoc($res_country);
            $country_id = $row_country['ID'];
            //update city table with country ID
            $update_query = "UPDATE city SET id_country = $country_id WHERE ID = $id";
            mysqli_query($conn, $update_query);       

        }

        $result = mysqli_query($conn, "SELECT * FROM countrylanguage");

        //fetch data and populate array
        while ($row = mysqli_fetch_assoc($result)) {

            //assign ID as key and Name as value
            $id = $row['ID'];
            $countrycode = $row['CountryCode'];

            $res_country = mysqli_query($conn, "SELECT ID FROM country WHERE Code = '$countrycode'");
            $row_country = mysqli_fetch_assoc($res_country);
            $country_id = $row_country['ID'];
            //update countrylanguage table with country ID
            $update_query = "UPDATE countrylanguage SET id_country = $country_id WHERE ID = $id";
            mysqli_query($conn, $update_query);

        }

        //print the array
        print_r($table_country);


?>