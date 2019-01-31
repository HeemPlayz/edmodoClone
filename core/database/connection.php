<?php
#data source name
    $dsn = 'mysql:host=localhost; dbname=edmodoCloneDB';
    $password='';
    $user='root';

    try{
        $pdo=new PDO($dsn,$user,$password);
    }catch(PDOException $e){
        echo 'Connection Error '.$e->getMessage();
    }
?> 