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
    <style>
        body {
            background-image: url('../img/b1.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <div class="grid grid-cols-1 lg:grid-cols-4 lg:h-screen overflow-hidden">
        <section class="col-span-1">
            <?php include('sideuser.php'); ?>
        </section>

        <section class="col-span-3 overflow-y-auto pb-20">
            <?php include('navbaruser.php'); ?>
            <div class="flex flex-col space-y-2 p-4 sm:p-6 md:p-8 lg:p-16 pt-16 h-full overflow-y-auto" id="chat-container">
                <?php include('chats.php'); ?>
            </div>
        </section>
    </div>

    <div class="fixed bottom-0 left-0 right-0 lg:ml-[400px] p-4">
        <form id="message-form" action="send.php" method="post" class="max-w-2xl mx-auto">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="message">Message:</label>
            <div class="flex items-center relative">
                <button type="button" id="emoji-button" class="absolute left-2 top-1/2 transform -translate-y-1/2">
                    <span class="text-2xl"><div>
                        <img src="../img/emoji.png" class="h-6 w-6" alt="">
                    </div></span>
                </button>
                <input class="shadow bg-white text-black appearance-none border rounded-l w-full py-2 pl-10 pr-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="message" name="message" type="text" placeholder="Enter message">
                <input type="hidden" name="recipient" value="<?php echo htmlspecialchars($selected_user); ?>">
                <button type="submit" class="py-2 ml-2 px-4 flex justify-center items-center bg-purple-600 hover:bg-purple-700 hover:scale-125 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-r">
                    <img src="../img/send.png" alt="" class="h-4 w-4">
                </button>
            </div>
        </form>
        <emoji-picker id="emoji-picker" class="hidden absolute bottom-16 right-4"></emoji-picker>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var previousContent = "";

            function fetchMessages() {
                var selected_user = "<?php echo htmlspecialchars($selected_user); ?>";

                $.ajax({
                    url: 'chats.php',
                    method: 'GET',
                    data: {
                        selected_user: selected_user
                    },
                    dataType: 'html',
                    success: function(response) {
                        if (response !== previousContent) {
                            $('#chat-container').html(response);
                            $('#chat-container').scrollTop($('#chat-container')[0].scrollHeight);
                            previousContent = response;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching messages:', error);
                    }
                });
            }

            fetchMessages();
            setInterval(fetchMessages, 2000);

            $('#message-form').submit(function(event) {
                event.preventDefault(); 

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: $(this).serialize(),
                    dataType: 'html',
                    success: function(response) {
                        fetchMessages(); 
                        $('#message').val(''); 
                    },
                    error: function(xhr, status, error) {
                        console.error('Error sending message:', error);
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
    <script>
        $(document).ready(function() {
            const emojiButton = document.querySelector('#emoji-button');
            const emojiPicker = document.querySelector('#emoji-picker');
            const messageInput = document.querySelector('#message');

            emojiButton.addEventListener('click', () => {
                emojiPicker.classList.toggle('hidden');
            });

            emojiPicker.addEventListener('emoji-click', event => {
                messageInput.value += event.detail.unicode;
                emojiPicker.classList.add('hidden');
            });

            document.addEventListener('click', (event) => {
                if (!emojiButton.contains(event.target) && !emojiPicker.contains(event.target)) {
                    emojiPicker.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>