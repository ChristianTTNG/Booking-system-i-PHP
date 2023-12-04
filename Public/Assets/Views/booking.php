<?php
 session_start();
 include("../../../Private/config.php");

// Check if the user is logged in
if (!isset($_SESSION['log'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$userData = check_login($con);

// Check if the user is a student
if ($userData['role'] !== 'Student') {
    header("Location: index.php");
    exit();
}

// Check if the form is submitted for booking
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $teacherId = $_POST['teacher'];
    $selectedTime = $_POST['selectedTime'];
    $course = $userData['currentModule']; // Assuming the course is the current module of the student

    // Handle the form submission, process booking
    // You need to implement this part based on your database structure and business logic.
    // Example: $student->bookCourse($teacherId, $course, $selectedTime);
    $studentId = $userData['id'];
    $sql = "INSERT INTO teacher_bookings (student_id, teacher_id, course, booking_time) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iiss", $studentId, $teacherId, $course, $selectedTime);
    $stmt->execute();
    $stmt->close();
}

// Fetch available teachers and their time slots from the database
$sql = "SELECT t.id AS teacher_id, u.firstname, u.lastname, t.availability 
        FROM teachers t
        INNER JOIN users u ON t.user_id = u.id";
$result = $con->query($sql);

// Fetch existing bookings for the student
$studentId = $userData['id'];
$sqlStudentBookings = "SELECT tb.id, u.firstname AS teacher_firstname, u.lastname AS teacher_lastname, tb.booking_time 
                       FROM teacher_bookings tb
                       INNER JOIN users u ON tb.teacher_id = u.id
                       WHERE tb.student_id = ?";
$stmtStudentBookings = $con->prepare($sqlStudentBookings);
$stmtStudentBookings->bind_param("i", $studentId);
$stmtStudentBookings->execute();
$resultStudentBookings = $stmtStudentBookings->get_result();
$studentBookings = $resultStudentBookings->fetch_all(MYSQLI_ASSOC);
$stmtStudentBookings->close();

// Fetch existing bookings for the teacher
if ($userData['role'] === 'Teacher') {
    $sqlTeacherBookings = "SELECT tb.id, u.firstname AS student_firstname, u.lastname AS student_lastname, tb.booking_time 
                           FROM teacher_bookings tb
                           INNER JOIN users u ON tb.student_id = u.id
                           WHERE tb.teacher_id = ?";
    $stmtTeacherBookings = $con->prepare($sqlTeacherBookings);
    $stmtTeacherBookings->bind_param("i", $userData['id']);
    $stmtTeacherBookings->execute();
    $resultTeacherBookings = $stmtTeacherBookings->get_result();
    $teacherBookings = $resultTeacherBookings->fetch_all(MYSQLI_ASSOC);
    $stmtTeacherBookings->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
</head>
<body>

    <h1>Booking Page</h1>

    <!-- Display available teachers and time slots -->
    <h2>Available Teachers</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="teacher">Select a Teacher:</label>
        <select name="teacher" id="teacher">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <option value="<?php echo $row['teacher_id']; ?>"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></option>
            <?php endwhile; ?>
        </select>

        <label for="time">Select a Time:</label>
        <input type="time" name="selectedTime" required>

        <button type="submit">Book Course</button>
    </form>

    <!-- Display existing bookings for the student - WORK IN PROGRESS -->
    <h2>Your Bookings</h2>
    <ul>
        <?php foreach ($studentBookings as $booking) : ?>
            <li><?php echo $booking['teacher_firstname'] . ' ' . $booking['teacher_lastname'] . ' at ' . $booking['booking_time']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Display existing bookings for the teacher - WORK IN PROGRESS --> 
    <?php if ($userData['role'] === 'Teacher') : ?>
        <h2>Teacher's Bookings</h2>
        <ul>
            <?php foreach ($teacherBookings as $booking) : ?>
                <li><?php echo $booking['student_firstname'] . ' ' . $booking['student_lastname'] . ' at ' . $booking['booking_time']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="../../index.php">Back to Main Page</a>

</body>
</html>
