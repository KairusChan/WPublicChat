<?php
session_start();

require_once "dbconnection.php";

$error = "";

// Define role-to-page mapping (centralized configuration)
$rolePages = [
    "admin" => "homepage/admin.php",
    "moderator" => "homepage/mod.php",
    "user" => "homepage/users.php"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {
                
                // Validate user role exists in our role mapping
                if (!isset($rolePages[$user["role"]])) {
                    $error = "User role is not configured";
                } else {
                    // Set session variables
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["role"] = $user["role"];
                    $_SESSION["login_time"] = time();

                    // Redirect to designated page based on role
                    $redirectPage = $rolePages[$user["role"]];
                    header("Location: " . $redirectPage);
                    exit;
                }
            } else {
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again later";
            error_log("Login database error: " . $e->getMessage());
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<div class="login-card">
    <h2>Login</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="username" required placeholder=" ">
            <label>Username</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" required placeholder=" ">
            <label>Password</label>
        </div>

        <button type="submit">Sign In</button>
    </form>

    <div class="footer-text">
        Don’t have an account? <a href="reg.php">Register</a>
    </div>
</div>

</body>
</html>
