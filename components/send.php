<?php
session_start();
include('connection.php');

if ($_SESSION['role'] !== 'customer' && $_SESSION['role'] !== 'admin') {
    header("location: ./components/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $recipient = isset($_POST['recipient']) ? $_POST['recipient'] : null;
    $user_email = $_SESSION['email'];

    // Get the sender's username
    $stmt = $conn->prepare("SELECT name FROM login WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    // Insert the message with recipient
    $query = "INSERT INTO messages (username, message, recipient, timestamp) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $message, $recipient);
   
    if ($stmt->execute()) {
        // Redirect back to the chat with the selected user
        header("Location: contents.php" . ($recipient ? "?selected_user=" . urlencode($recipient) : ""));
        exit();
    } else {
        echo "Error inserting message: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>