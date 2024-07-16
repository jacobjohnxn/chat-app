<?php
include('connection.php');
session_start();
$user_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT name FROM login WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

$selected_user = isset($_GET['selected_user']) ? $_GET['selected_user'] : null;

if ($selected_user) {
    $query = "SELECT id, username, message, timestamp FROM messages WHERE (username = ? AND recipient = ?) OR (username = ? AND recipient = ?) ORDER BY timestamp ASC";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssss", $username, $selected_user, $selected_user, $username);

    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $msg_username = htmlspecialchars($row['username']);
        $message = htmlspecialchars($row['message']);
        $chat_class = ($msg_username === $username) ? 'chat-end' : 'chat-start';
        $bubble_class = ($msg_username === $username) ? 'bg-purple-600 border border-black text-white' : 'bg-gray-100 border border-black text-black';
        $timestamp = date('M d, H:i', strtotime($row['timestamp']));

        echo "<div class='chat $chat_class'>";
        echo "<div class='chat-header'>";
        // echo "<span class='font-bold text-orange-500 text-lg sm:text-xl md:text-2xl'>$msg_username</span>";
        echo "</div>";
        echo "<div class='chat-bubble relative transition-transform duration-300 transform hover:scale-105 $bubble_class'>";
        echo "<p class='sm:text-lg md:text-xl'>$message</p>";
        echo "<time class='text-xs sm:text-sm opacity-50 ml-2'>$timestamp</time>";
        echo "</div>";
        echo "</div>";
    }
    $stmt->close();
} else {
    echo "<div class='flex items-center justify-center h-full w-full'>
            <span class='bg-purple-500 loading loading-spinner loading-lg'></span>
          </div>";
}
?>
