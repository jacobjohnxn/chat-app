<?php

$host = "127.0.0.1";
$user = "root";
$pass = "";
$database = "chat";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $database, $port);

$sql = "SELECT name FROM login";
$result = $conn->query($sql);

$usersname = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row;
    }
}

$sql = "SELECT * FROM  login WHERE role='user' ";
$result = $conn->query($sql);

$email = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    header("location: ./login.php");
    session_unset();
    session_destroy();
    exit();
}
