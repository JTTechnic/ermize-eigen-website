<?php

require_once __DIR__ . '/../../config/database.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password);

        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                header("Location: index.php?message=" . urlencode("Login succesvol"));
                exit();
            } else {
                header("Location: /public/login.php?error=" . urlencode("Ongeldig wachtwoord"));
                exit();
            }
        } else {
            header("Location: /public/login.php?error=" . urlencode("Gebruiker niet gevonden"));
            exit();
        }
    }
} elseif ($type === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password);
        $confirm_password = $conn->real_escape_string($confirm_password);

        if ($password === $confirm_password) {
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = $conn->query($sql);

            if ($result->num_rows === 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
                if ($conn->query($sql) === TRUE) {
                    header("Location: /public/index.php?message=" . urlencode("Registratie succesvol"));
                    exit();
                } else {
                    header("Location: /public/register.php?error=" . urlencode("Fout bij registratie: " . $conn->error));
                    exit();
                }
            } else {
                header("Location: /public/register.php?error=" . urlencode("Gebruiker bestaat al"));
                exit();
            }
        } else {
            header("Location: /public/register.php?error=" . urlencode("Wachtwoorden komen niet overeen"));
            exit();
        }
    }
} else {
    $redirect_url = ($type === 'register') ? 'register.php' : 'login.php';
    header("Location: /public/$redirect_url?error=" . urlencode("Ongeldig type opgegeven"));
    exit();
}

$conn->close();
