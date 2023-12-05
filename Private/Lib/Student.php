<?php

class Student {
    private $con;

    public function __construct($database) {
        $this->con = $database->getConnection();
    }

    public function getStudentInfo($studentId) {
        // Example query to retrieve student information by ID
        $query = "SELECT * FROM user WHERE id = ? AND role = 'Student'";
        $stmt = mysqli_prepare($this->con, $query);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "i", $studentId);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);

        // Check if the query returned a row
        if ($result && mysqli_num_rows($result) > 0) {
            // Student data found, return student information
            return mysqli_fetch_assoc($result);
        } else {
            // Student not found
            return false;
        }
    }

    // Add more methods as needed for student-related operations

}

?>
