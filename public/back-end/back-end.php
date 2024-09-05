<?php

session_start();

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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                header("Location: /?message=" . urlencode("Login successful"));
                exit();
            } else {
                header("Location: /login.php?error=" . urlencode("Invalid password"));
                exit();
            }
        } else {
            header("Location: /login.php?error=" . urlencode("User not found"));
            exit();
        }
    }
} elseif ($type === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        
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
                    $user_id = $conn->insert_id;
                    
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    
                    header("Location: /?message=" . urlencode("Registration succesful, you're now logged in."));
                    exit();
                } else {
                    header("Location: /register.php?error=" . urlencode("Error with registration: " . $conn->error));
                    exit();
                }
            } else {
                header("Location: /register.php?error=" . urlencode("User already registered"));
                exit();
            }
        } else {
            header("Location: /register.php?error=" . urlencode("Passwords do not match"));
            exit();
        }
    }
} elseif ($type === 'update') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['user_id'];
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);

        if ($password && $confirm_password) {
            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username='$username', email='$email', password='$hashed_password' WHERE id='$user_id'";
            } else {
                header("Location: /update.php?error=" . urlencode("Passwords do not match"));
                exit();
            }
        } else {
            $sql = "UPDATE users SET username='$username', email='$email' WHERE id='$user_id'";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: /?message=" . urlencode("Profile is succesfully updated"));
            exit();
        } else {
            header("Location: /update.php?error=" . urlencode("Error while updating: " . $conn->error));
            exit();
        }
    }
} elseif ($type === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        session_start();
        
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            
            $sql = "DELETE FROM users WHERE id='$user_id'";
            if ($conn->query($sql) === TRUE) {
                session_unset();
                session_destroy();
                
                header("Location: /?message=" . urlencode("Profile succesfully deleted. You've been logged out."));
                exit();
            } else {
                header("Location: /update.php?error=" . urlencode("Error while deleting: " . $conn->error));
                exit();
            }
        } else {
            header("Location: /login.php?error=" . urlencode("You have to be logged in to delete your profile."));
            exit();
        }
    }
} elseif ($type === 'password_reset') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? $_POST['email'] : '';

        $email = $conn->real_escape_string($email);

        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(32));

            $sql = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
            if ($conn->query($sql) === TRUE) {
                $reset_link = "http://localhost/reset-password.php?token=$token";
                $to = $email;
                $subject = "Wachtwoord Herstel Verzoek";
                $message = "Klik op de volgende link om je wachtwoord te herstellen: $reset_link";
                $headers = "From: no-reply@example.com";

                if (mail($to, $subject, $message, $headers)) {
                    header("Location: /login.php?message=" . urlencode("Een e-mail met instructies is verzonden."));
                    exit();
                } else {
                    header("Location: /login.php?error=" . urlencode("Fout bij het verzenden van de e-mail."));
                    exit();
                }
            } else {
                header("Location: /login.php?error=" . urlencode("Fout bij het opslaan van het token: " . $conn->error));
                exit();
            }
        } else {
            header("Location: /login.php?error=" . urlencode("E-mailadres niet gevonden."));
            exit();
        }
    }
} else {
    $redirect_url = ($type === 'register') ? 'register.php' : 'login.php';
    header("Location: /$redirect_url?error=" . urlencode("Invalid type"));
    exit();
}

$conn->close();
