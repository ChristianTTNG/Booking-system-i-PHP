<?php
session_start();
include("../../../Private/config.php");

// Check if the user is logged in
if (!isset($_SESSION['log'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Fetch user data from the database based on the logged-in username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $_SESSION['log']);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Output HTML header
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
</head>
<body>

    <h1>Welcome, <?php echo $user_data['firstname'] . ' ' . $user_data['lastname']; ?>!</h1>

    <?php
    // Check if the user is a student 
    if ($user_data['role'] === 'Student') {
        // Display student-specific information
        echo '<p>You are a student in module: ' . $user_data['currentModule'] . '</p>'; //NB! Skal egentlig vise hvilken module man er i, men vi har ikke det som column i users tabellen
        echo '<p>Preferred Booking Time: ' . $user_data['preferredTime'] . '</p>';
    } elseif ($user_data['role'] === 'Teacher') {
        // Display teacher-specific information
        echo '<p>You are a teacher in course: ' . $user_data['preferredTeacher'] . '</p>';
        echo '<p>Preferred Booking Time: ' . $user_data['preferredTime'] . '</p>';
    }

    // Add a link to log out
    echo '<p><a href="logout.php">Log Out</a></p>';
    ?>

</body>
</html>
