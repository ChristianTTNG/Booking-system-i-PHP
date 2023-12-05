<?php
session_start();

// Include config
include("../../../Private/config.php");

echo "Session log: " . $_SESSION['log'] . "<br>";

// Check if the user is logged in
if (!isset($_SESSION['log'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted for sending a message
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ensure the required fields are set
    if (isset($_POST['receiverId']) && isset($_POST['messageContent'])) {
        $receiverId = $_POST['receiverId'];
        $messageContent = $_POST['messageContent'];

        // Get the sender's ID from the session
        $senderId = $_SESSION['logId'];

        echo "Sender ID: $senderId<br>";
        echo "Receiver ID: $receiverId<br>";
       
        // Insert the message into the database
        $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'iis', $senderId, $receiverId, $messageContent);

        if (mysqli_stmt_execute($stmt)) {
            // Close the statement
            mysqli_stmt_close($stmt);

            // Redirect back to message.php
            header("Location: message.php");
            exit();
        } else {
            // Handle the case where the message insertion fails
            echo "Error sending message.";
        }
    }
}

// Fetch messages for the logged-in user
$userId = $_SESSION['logId'];
$query = "SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'ii', $userId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch all messages
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the statement
mysqli_stmt_close($stmt);

$query = "SELECT id, firstname, lastname FROM users WHERE id <> ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch all users
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Create an array to store user names
$userNames = [];
foreach ($users as $user) {
    $userNames[$user['id']] = $user['firstname'] . ' ' . $user['lastname'];
}

// Close the statement
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Page</title>
</head>
<body>

    <h1>Message Page</h1>

   <!-- Display user's messages -->
    <h2>Your Messages</h2>
    <ul>
        <?php foreach ($messages as $msg) : ?>
            <?php
                $senderId = $msg['sender_id'];
                $isFromYou = ($senderId == $userId);
                $senderName = $isFromYou ? 'You' : $userNames[$senderId];
            ?>
            <li><?php echo "From: " . $senderName . ', Message: ' . $msg['message'] . ', Time: ' . $msg['timestamp']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Form for sending a message -->
    <h2>Send a Message</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="receiver">Select Receiver:</label>
        <select name="receiverId" id="receiver">
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="message">Message:</label>
        <textarea name="messageContent" id="message" rows="4" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <a href="index.php">Back to Main Page</a>

</body>
</html>
