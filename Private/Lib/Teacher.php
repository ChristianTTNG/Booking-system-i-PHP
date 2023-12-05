<?php

class Teacher {
    private $con;

    public function __construct($database) {
        $this->con = $database->getConnection();
    }

    public function getTeacherInfo($teacherId) {
        // Example query to retrieve teacher information by ID
        $query = "SELECT * FROM user WHERE id = ? AND role = 'Teacher'";
        $stmt = mysqli_prepare($this->con, $query);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "i", $teacherId);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);

        // Check if the query returned a row
        if ($result && mysqli_num_rows($result) > 0) {
            // Teacher data found, return teacher information
            return mysqli_fetch_assoc($result);
        } else {
            // Teacher not found
            return false;
        }
    }

    // Add more methods as needed for teacher-related operations

}

?>
