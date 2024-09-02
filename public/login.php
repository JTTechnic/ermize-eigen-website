<!DOCTYPE html>
<html lang="en">
<?php require_once('includes/head.php'); ?>
<body>
    <main>
        <form action="backend/backend.php?type=login" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password">
            </div>
            <div class="form-group">
                <button>Submit</button>
            </div>
        </form>
    </main>
</body>
</html>
