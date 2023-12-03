<?php

class User {
    private $con;

    public function __construct($database) {
        $this->con = $database->getConnection();
    }

    public function checkLogin($username, $password) {
        // Example query to check login credentials
        $query = "SELECT * FROM user WHERE username = ? AND password = ?";
        $stmt = mysqli_prepare($this->con, $query);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);

        // Check if the query returned a row
        if ($result && mysqli_num_rows($result) > 0) {
            // User is authenticated, return user data
            return mysqli_fetch_assoc($result);
        } else {
            // User credentials are not valid
            return false;
        }
    }

    public function getUserInfo($userId) {
        // Example query to retrieve user information by ID
        $query = "SELECT * FROM user WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $query);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "i", $userId);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);

        // Check if the query returned a row
        if ($result && mysqli_num_rows($result) > 0) {
            // User data found, return user information
            return mysqli_fetch_assoc($result);
        } else {
            // User not found
            return false;
        }
    }

    // Add more methods as needed for user-related operations

}

?>
