<!DOCTYPE html>
<html lang="en">
<?php require_once('includes/head.php'); ?>
<body>
    <main>
        <form action="back-end/back-end.php?type=login" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button>Submit</button>
            </div>
        </form>
        <form action="back-end/back-end.php?type=password_reset" method="post">
            <div class="form-group">
                <button>I forgot my password</button>
            </div>
        </form>
    </main>
</body>
</html>
