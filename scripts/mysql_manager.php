<?php


class MySqlManager
{
    private $link;

    function __construct($hostname, $username, $pass, $dbname)
    {
        $this->link = mysqli_connect($hostname, $username, $pass, $dbname);
        if ($this->link == false) {
            print("Could not open connection " . mysqli_connect_error());
        } else {
            print("Connection was established\n");
        }
    }

    function get_user($username)
    {

        if ($this->link == false) {
            print "Connection was not opened";
            return false;
        }
        $query = 'SELECT * from users WHERE login = ?';

        $stmt = mysqli_stmt_init($this->link);

        if (!mysqli_stmt_prepare($stmt, $query)) {
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

    function create_task($username, $task_info)
    {
        if ($this->link == false) {
            print "Connection was not opened";
            return false;
        }

        $query = 'INSERT INTO tasks (name, owner, contractor, description, finish_date, status) VALUES (?, ?, ?, ?, ?, ?)';

        $stmt = mysqli_stmt_init($this->link);

        if (!$stmt->prepare($query)) {
            print "Failed to prepare statement\n";
            var_dump($stmt->error);
            return false;
        }

        $owner = $this->get_user($username);
        $contractor = $this->get_user($task_info['contractor']);

        if ($owner == false) {
            print "Could not get owner from DB\n";
            return false;
        }

        if ($contractor == false) {
            print "Could not get contractor from DB\n";
            return false;
        }


        // team check
        if ($contractor['team'] != $owner['team']){
            print "Contractor is from different team";
            return false;
        }
        $stmt->bind_param('siisss',
            $task_info['task_name'],
            $owner['id'],
            $contractor['id'],
            $task_info['description'],
            $task_info['date'],
            $task_info['status']
        );

        mysqli_stmt_execute($stmt);

        $results = mysqli_stmt_get_result($stmt);

        if ($results == false){
            var_dump($stmt->error);
        }

        return $results;
    }
}