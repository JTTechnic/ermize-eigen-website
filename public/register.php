<!DOCTYPE html>
<html lang="en">
<?php require_once('includes/head.php'); ?>
<link rel="stylesheet" href="css/login.css">
<body>
    <main>
        <form action="backend/backend.php?type=register" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <button>Submit</button>
            </div>
        </form>
    </main>
</body>
</html>
