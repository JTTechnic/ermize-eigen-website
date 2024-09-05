<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once('includes/head.php'); ?>
<body>
    <main>
        <a href="index.php">Return</a>
        <form action="back-end/back-end.php?type=update" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (Keep empty in case you don't want to change your password):</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
            </div>
        </form>

        <form action="back-end/back-end.php?type=delete" method="post">
            <button type="submit" class="delete-btn">Delete Profile</button>
        </form>
    </main>
</body>
</html>
