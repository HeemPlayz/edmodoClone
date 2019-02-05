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
            $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE email=? AND password = ?");
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

        public function userData($user_id){
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id=?");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        }

        public function logout(){
            $_SESSION = array();
            session_destroy();
            header('Location: '.BASE_URL.'index.php');
        }

        public function checkUsername($username){
            $stmt = $this->pdo->prepare("SELECT username FROM users WHERE username=? ");
            $stmt->execute([$username]);

            $count = $stmt->rowCount();
            if($count > 0){
                return true;
            }else{
                return false;
            }
        }
        public function checkEmail($email){
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email=? ");
            $stmt->execute([$email]);

            $count = $stmt->rowCount();
            if($count > 0){
                return true;
            }else{
                return false;
            }
        }

        public function register($email,$screenName,$password){
                $profileImage = 'assets/images/CKC.jpg';
                $profileCover = 'assets/images/CKC.jpg';
            try{

                $stmt=$this->pdo->prepare("INSERT INTO `users`(`email`, `password`, `screenName`, `profileImage`, `profileCover`) VALUES (?,?,?,?,?)");
                $stmt->execute([$email,md5($password),$screenName,$profileImage,$profileCover]);
                
                $user_id = $this->pdo->lastInsertID();
                $_SESSION['user_id']=$user_id;

            }catch(PDOException $e){
                echo 'Error Func Register '.$e->getMessage();
            }
        
        }

        public function create($table,$fields=array()){
            $columns = implode(',',array_keys($fields));
            $values = ':'.implode(', :', array_keys($fields));
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";

           
            if($stmt=$this->pdo->prepare($sql)){
                foreach($fields as $key => $data){
                    $stmt->bindValue(':'.$key, $data);
                }
                $stmt->execute();
                return $this->pdo->lastInsertId();
               
            }
        }

        public function update($table, $user_id,$fields = array()){

            $columns ='';
            $fieldCounter = 1;
            
            foreach($fields as $name => $value){
                $columns .= "{$name} = :{$name}";
                if($fieldCounter<count($fields)){
                    $columns .=', ';
                }
                $fieldCounter++;
            }

        $sql = "UPDATE {$table} SET {$columns} WHERE user_id = {$user_id}";
            if($stmt=$this->pdo->prepare($sql)){
                  foreach($fields as $key => $value){
                      $stmt->bindValue(':'.$key, $value);
                  } 
                  $stmt->execute();
            }
            //var_dump($sql);
        }

        public function userIdByUsername($username){
            $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE username=?");
            $stmt->execute([$username]);
            $user=$stmt->fetch(PDO::FETCH_OBJ);

            return $user->user_id;

        }

        public function loggedIn(){
            return (isset($_SESSION['user_id'])) ? true : false;
        }
    }
?>