<?php

class Database {
    private $con;
    public function __construct($dbhost, $dbuser, $dbpass, $dbname) {
        $this->con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
    }

    public function getConnection() {
        return $this->con;
    }

    // Additional database methods can be added here
}

