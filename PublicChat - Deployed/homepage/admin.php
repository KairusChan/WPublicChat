<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="../style.css">
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Admin Dashboard</h1>

<p>
    Welcome, <?= htmlspecialchars($_SESSION["username"]) ?>!
</p>


<a href="logout.php">Logout</a>

</body>
</html>