<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

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
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if ($password === $confirm_password) {
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sss', $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    $user_id = $conn->insert_id;

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;

                    header("Location: /?message=" . urlencode("Registration successful, you're now logged in."));
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

        if ($password && $confirm_password) {
            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssi', $username, $email, $hashed_password, $user_id);
            } else {
                header("Location: /update.php?error=" . urlencode("Passwords do not match"));
                exit();
            }
        } else {
            $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssi', $username, $email, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: /?message=" . urlencode("Profile successfully updated"));
            exit();
        } else {
            header("Location: /update.php?error=" . urlencode("Error while updating: " . $conn->error));
            exit();
        }
    }
} elseif ($type === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);

            if ($stmt->execute()) {
                session_unset();
                session_destroy();

                header("Location: /?message=" . urlencode("Profile successfully deleted. You've been logged out."));
                exit();
            } else {
                header("Location: /update.php?error=" . urlencode("Error while deleting: " . $conn->error));
                exit();
            }
        } else {
            header("Location: /login.php?error=" . urlencode("You need to be logged in to delete your profile."));
            exit();
        }
    }
} elseif ($type === 'password_reset') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = isset($_POST['email']) ? $_POST['email'] : '';

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(32));

            $sql = "INSERT INTO password_resets (email, token) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $email, $token);

            if ($stmt->execute()) {
                header("Location: /password-reset.php?token=$token");
                exit();
            } else {
                header("Location: /login.php?error=" . urlencode("Error saving the token: " . $conn->error));
                exit();
            }
        } else {
            header("Location: /login.php?error=" . urlencode("Email address not found."));
            exit();
        }
    }
} else {
    $redirect_url = ($type === 'register') ? 'register.php' : 'login.php';
    header("Location: /$redirect_url?error=" . urlencode("Invalid type"));
    exit();
}

$conn->close();
?>
