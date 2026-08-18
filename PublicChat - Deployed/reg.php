<?php
session_start();

require_once "dbconnection.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    // Validate fields
    if ($name === "" || $username === "" || $password === "" || $confirmPassword === "") {

        $error = "All fields are required.";

    } elseif ($password !== $confirmPassword) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } else {

        try {

            // Check if username already exists
            $stmt = $pdo->prepare("
                SELECT id
                FROM users
                WHERE username = ?
                LIMIT 1
            ");

            $stmt->execute([$username]);

            if ($stmt->fetch()) {

                $error = "Username is already taken.";

            } else {

                // Hash password
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // New accounts are ALWAYS normal users
                $role = "user";

                // Insert account
                $stmt = $pdo->prepare("
                    INSERT INTO users
                    (name, username, password, role)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $name,
                    $username,
                    $hashedPassword,
                    $role
                ]);

                $success = "Account created successfully! You can now log in.";
            }

        } catch (PDOException $e) {

            error_log("Registration error: " . $e->getMessage());

            $error = "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Register</title>

    <link rel="stylesheet" href="index.css">

</head>

<body>

<div class="login-card">

    <h2>Create Account</h2>

    <?php if ($error !== ""): ?>

        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>


    <?php if ($success !== ""): ?>

        <p class="success">
            <?= htmlspecialchars($success) ?>
        </p>

    <?php endif; ?>


    <?php if ($success === ""): ?>

        <form method="POST">

            <div class="input-group">

                <input
                    type="text"
                    name="name"
                    required
                    placeholder=" "
                >

                <label>Full Name</label>

            </div>


            <div class="input-group">

                <input
                    type="text"
                    name="username"
                    required
                    placeholder=" "
                    autocomplete="username"
                >

                <label>Username</label>

            </div>


            <div class="input-group">

                <input
                    type="password"
                    name="password"
                    required
                    placeholder=" "
                    autocomplete="new-password"
                >

                <label>Password</label>

            </div>


            <div class="input-group">

                <input
                    type="password"
                    name="confirm_password"
                    required
                    placeholder=" "
                    autocomplete="new-password"
                >

                <label>Confirm Password</label>

            </div>


            <button type="submit">
                Register
            </button>

        </form>

    <?php endif; ?>


    <div class="footer-text">

        Already have an account?
        <a href="index.php">Login</a>

    </div>

</div>

</body>

</html>