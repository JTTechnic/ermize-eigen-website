<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERMize</title>

    <?php
    $page = basename($_SERVER['PHP_SELF'], ".php");

    if (in_array($page, ['login', 'register', 'profile'])) {
        echo '<link rel="stylesheet" href="css/login.css">';
    } else {
        echo '<link rel="stylesheet" href="css/style.css">';
    }
    ?>
</head>
