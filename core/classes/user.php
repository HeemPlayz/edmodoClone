<?php

    class User{
        protected $pdo;

        function __construct($pdo){
            $this->pdo = $pdo;
        }

        public function checkInput($var){
            $var = htmlspecialchars($var);
            $var = trim($var);
            $var = stripslashes($var);

            return $var;
        }

        public function login($email,$password){
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=? AND password = ?");
            // $stmt-> bindParam(":email",$email,PDO::PARAM_STR);
            // $stmt-> bindParam(":password",md5($password),PDO::PARAM_STR);
           $stmt->execute([$email,md5($password)]);

            $user=$stmt->fetch(PDO::FETCH_OBJ);
            $count=$stmt->rowCount();

           if($count>0){
               $_SESSION['user_id']=$user->user_id;
            
               header('Location:home.php');
           }else{
               return false;
           }
        }
    }
?>