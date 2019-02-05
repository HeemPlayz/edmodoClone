<?php

    include 'database/connection.php';

    include 'classes/user.php';
    include 'classes/tweet.php';
    include 'classes/follow.php';

    global $pdo;

    session_start();
    
    $getFromUserClass = new User($pdo);
    $getFromTweetClass = new Tweet($pdo);
    $getFromFollowClass = new User($pdo);

    define("BASE_URL", "http://localhost/edmodoClone/");
?>