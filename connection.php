<?php
function connection(){
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "dlms";

    $con = new mysqli($host, $username, $password, $database);

    if ($con->connect_error){
        die("Connection failed: " . $con->connect_error);
    }
    return $con;
}
?>
