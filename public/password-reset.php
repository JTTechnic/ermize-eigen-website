<?php

require_once __DIR__ . '/../../config/database.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header("Location: /login.php?error=" . urlencode("Invalid token."));
    exit();
}

$token = $conn->real_escape_string($token);

$sql = "SELECT * FROM password_resets WHERE token='$token'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $row = $result->fetch_assoc();
            $email = $row['email'];

            $update_sql = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
            if ($conn->query($update_sql) === TRUE) {
                $delete_sql = "DELETE FROM password_resets WHERE token='$token'";
                $conn->query($delete_sql);

                header("Location: /login.php?message=" . urlencode("Password successfully updated. You can now log in."));
                exit();
            } else {
                header("Location: /login.php?error=" . urlencode("Error while updating the password: " . $conn->error));
                exit();
            }
        } else {
            header("Location: /reset-password.php?token=" . urlencode($token) . "&error=" . urlencode("Passwords do not match."));
            exit();
        }
    }
} else {
    header("Location: /login.php?error=" . urlencode("Invalid or expired token."));
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once('head.php'); ?>
<body>
    <main>
        <?php
        if (isset($_GET['error'])) {
            echo '<p style="color: red;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if (isset($_GET['message'])) {
            echo '<p style="color: green;">' . htmlspecialchars($_GET['message']) . '</p>';
        }
        ?>
        <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
            <div>
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div>
                <button type="submit">Update Password</button>
            </div>
        </form>
    </main>
</body>
</html>
