<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: index.php");
    exit;
}

require_once "../dbconnection.php";

$userId = $_SESSION["user_id"];
$username = $_SESSION["username"];

$messageError = "";

/*
|--------------------------------------------------------------------------
| Send Message
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_message"])) {

    $message = trim($_POST["message"] ?? "");

    if ($message === "") {
        $messageError = "Message cannot be empty.";
    } elseif (mb_strlen($message) > 1000) {
        $messageError = "Message is too long.";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO public_messages (user_id, message)
            VALUES (:user_id, :message)
        ");

        $stmt->execute([
            ":user_id" => $userId,
            ":message" => $message
        ]);

        /*
         * Redirect after sending so refreshing the page
         * doesn't send the same message again.
         */
        header("Location: users.php");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Get Public Messages
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        public_messages.id,
        public_messages.message,
        public_messages.created_at,
        users.username,
        users.name,
        users.role
    FROM public_messages
    INNER JOIN users
        ON public_messages.user_id = users.id
    ORDER BY public_messages.created_at ASC
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Dashboard</title>

    <link rel="stylesheet" href="css/users.css">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo">
            <span>💬</span>
            ChatDB
        </div>

        <div class="user-profile">

            <div class="avatar">
                <?= strtoupper(substr($_SESSION["username"], 0, 1)) ?>
            </div>

            <div class="user-info">

                <strong>
                    <?= htmlspecialchars($_SESSION["username"]) ?>
                </strong>

                <span>
                    <?= htmlspecialchars($_SESSION["role"]) ?>
                </span>

            </div>

        </div>


        <nav class="navigation">

            <a href="users.php" class="nav-item active">
                <span>💬</span>
                Public Chat
            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="logout.php" class="logout">
                <span>↪</span>
                Logout
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="topbar">

            <div>
                <h1>Public Chat</h1>

                <p>
                    Welcome,
                    <strong>
                        <?= htmlspecialchars($_SESSION["username"]) ?>
                    </strong>
                </p>
            </div>

            <div class="online-status">
                <span class="status-dot"></span>
                Online
            </div>

        </header>


        <!-- CHAT -->
        <section class="chat-container">

            <div class="chat-header">

                <div>

                    <h2>
                        🌐 Public Chat
                    </h2>

                    <p>
                        Everyone can chat here
                    </p>

                </div>

            </div>


            <!-- MESSAGES -->
            <div class="messages" id="messages">

                <?php if (empty($messages)): ?>

                    <div class="empty-chat">

                        <div class="empty-icon">
                            💬
                        </div>

                        <h3>
                            No messages yet
                        </h3>

                        <p>
                            Be the first person to say something!
                        </p>

                    </div>

                <?php else: ?>

                    <?php foreach ($messages as $msg): ?>

                        <div class="message">

                            <div class="message-avatar">

                                <?= strtoupper(
                                    substr(
                                        $msg["username"],
                                        0,
                                        1
                                    )
                                ) ?>

                            </div>


                            <div class="message-content">

                                <div class="message-header">

                                    <strong>
                                        <?= htmlspecialchars($msg["username"]) ?>
                                    </strong>

                                    <span class="role-badge role-<?= htmlspecialchars($msg["role"]) ?>">
                                        <?= htmlspecialchars($msg["role"]) ?>
                                    </span>

                                    <time>
                                        <?= date(
                                            "M d, Y • h:i A",
                                            strtotime($msg["created_at"])
                                        ) ?>
                                    </time>

                                </div>


                                <div class="message-text">

                                    <?= nl2br(
                                        htmlspecialchars($msg["message"])
                                    ) ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>


            <!-- SEND MESSAGE -->
            <div class="chat-input-area">

                <?php if ($messageError): ?>

                    <div class="message-error">
                        <?= htmlspecialchars($messageError) ?>
                    </div>

                <?php endif; ?>


                <form method="POST" class="message-form">

                    <textarea
                        name="message"
                        placeholder="Type a message..."
                        maxlength="1000"
                        rows="1"
                        required
                    ></textarea>

                    <button
                        type="submit"
                        name="send_message"
                    >
                        Send
                    </button>

                </form>

                <div class="chat-hint">
                    Messages are visible to everyone in Public Chat.
                </div>

            </div>

        </section>

    </main>

</div>


<script>

/*
|--------------------------------------------------------------------------
| Automatically scroll to newest message
|--------------------------------------------------------------------------
*/

const messages = document.getElementById("messages");

if (messages) {
    messages.scrollTop = messages.scrollHeight;
}


/*
|--------------------------------------------------------------------------
| Press Enter to send
| Shift + Enter = new line
|--------------------------------------------------------------------------
*/

const textarea = document.querySelector("textarea");

if (textarea) {

    textarea.addEventListener("keydown", function(event) {

        if (event.key === "Enter" && !event.shiftKey) {

            event.preventDefault();

            this.closest("form").submit();

        }

    });

}


const messages = document.getElementById("messages");

if (messages) {
    messages.scrollTop = messages.scrollHeight;
}

const textarea = document.querySelector("textarea");

if (textarea) {

    textarea.addEventListener("keydown", function(event) {

        if (event.key === "Enter" && !event.shiftKey) {

            event.preventDefault();

            this.closest("form").submit();

        }

    });

}