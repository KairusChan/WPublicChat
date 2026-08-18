<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "moderator") {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Moderator</title>
</head>
<body>

<h1>Moderator Dashboard</h1>

<p>
    Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!
</p>

<p>You are logged in as: <strong>Moderator</strong></p>

<a href="logout.php">Logout</a>

</body>
</html>