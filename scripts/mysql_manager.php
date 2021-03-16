<?php

function try_mysql_connection() {
    $connection_status = mysqli_connect("localhost", "root", "");
    if ($connection_status == false){
        print("Could not open connection " . mysqli_connect_error());
    }else{
        print("Connection was established");
    }
    return $connection_status;
}

