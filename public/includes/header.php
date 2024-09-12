<?php
session_start();
?>

<header>
    <div class="logo">
        <img src="img/logo.svg" alt="logo" width="100px">
        <h1>ERMize</h1>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
