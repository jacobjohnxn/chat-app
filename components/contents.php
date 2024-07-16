<?php
session_start();
include('connection.php');
if ($_SESSION['role'] !== 'customer' && $_SESSION['role'] !== 'admin') {
    header("location: login.php");
    exit();
}
$user_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT name FROM login WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Get the selected user from GET parameter
$selected_user = isset($_GET['selected_user']) ? $_GET['selected_user'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    body {
        background-image: url('../img/b1.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>

<body class="min-h-screen flex flex-col">
    <div class="grid grid-cols-1 lg:grid-cols-4  lg:h-screen overflow-hidden">
        <section class="col-span-1">
            <?php include('sideuser.php'); ?>
        </section>

        <section class="col-span-3 overflow-y-auto pb-20">
            <?php include('navbaruser.php'); ?>
            <div class="flex flex-col space-y-2 p-4 sm:p-6 md:p-8 lg:p-16 pt-16">
                <?php
                include('chats.php');
                ?>
            </div>
        </section>
    </div>

    <div class="fixed bottom-0 left-0 right-0  lg:ml-[400px] p-4 ">
        <form action="send.php" method="post" class="max-w-2xl mx-auto">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="message">Message:</label>
            <div class="flex items-center">
                <input class="shadow bg-white text-black appearance-none border rounded-l w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="message" name="message" type="text" placeholder="Enter message">
                <input type="hidden" name="recipient" value="<?php echo ($selected_user); ?>">
                <button class="py-2 ml-2 px-4 flex justify-center items-center bg-purple-600 hover:bg-purple-700 hover:scale-125 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-r">
                    <img src="../img/send.png" alt="" class="h-4 w-4">
                </button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
</body>

</html>