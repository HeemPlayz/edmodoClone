<?php
    include '../core/init.php';
    $getFromUserClass->logout();
    if($getFromUserClass->loggedIn()===false){
        header('Location: index.php');
    }
?>