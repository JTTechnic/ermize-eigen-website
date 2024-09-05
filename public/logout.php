<?php
session_start();

session_unset();

session_destroy();

header("Location: /?message=" . urlencode("You've been logged out succesfully."));
exit();
?>
