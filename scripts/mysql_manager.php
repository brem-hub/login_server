<?php


class MySqlManager{
    private $link;

    function __construct($hostname, $username, $pass, $dbname){
        $this->link = mysqli_connect($hostname, $username, $pass, $dbname);
        if ($this->link == false) {
            print("Could not open connection " . mysqli_connect_error());
        } else {
            print("Connection was established\n");
        }
    }

    function get_user($username){

        if ($this->link == false){
            print "Connection was not opened";
            return false;
        }
        $query = 'SELECT * from users WHERE login = ?';

        $stmt = mysqli_stmt_init($this->link);

        if(!mysqli_stmt_prepare($stmt, $query))
        {
            print "Failed to prepare statement\n";
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);

        $results = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($results) == 0) {
            return false;
        }
        // check for number of rows
        return mysqli_fetch_array($results);
    }
}

function try_mysql_connection()
{
    $connection_status = mysqli_connect("localhost", "root", "", "workdb");
    if ($connection_status == false) {
        print("Could not open connection " . mysqli_connect_error());
    } else {
        print("Connection was established");
    }
    return $connection_status;
}


function get_user(mysqli $link, $user_name)
{
    $query = 'SELECT * from users WHERE login = ?';


    $stmt = mysqli_stmt_init($link);

    if(!mysqli_stmt_prepare($stmt, $query))
    {
        print "Failed to prepare statement\n";
        return false;
    }

    mysqli_stmt_bind_param($stmt, 's', $user_name);
    mysqli_stmt_execute($stmt);

    $results = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($results) == 0) {
        return false;
    }
    // check for number of rows
    return mysqli_fetch_array($results);
}
