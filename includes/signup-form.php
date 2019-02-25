<?php

// $profileImage = 'assets/images/defaultProfileImage.png';
// $profileCover = 'assets/images/defaultCoverImage.png';
				
try{
	if(isset($_POST['signup'])){
		$screenName = $_POST['screenName'];
		$password = $_POST['password'];
		$email = $_POST['email'];

		if(empty($screenName) or empty($password) or empty($email)){
			$error = 'All Fields are required';
		}else{
			$email = $getFromUserClass->checkInput($email);
			$screenName = $getFromUserClass->checkInput($screenName);
			$password = $getFromUserClass->checkInput($password);
			if(!filter_var($email)){
				$error = 'Invalid Email Format';
			}else if(strlen($screenName)>20 or strlen($screenName)<6){
				$error = 'Screen name must be between in 6-20 characters';
			}else if(strlen($password)<5){
				$error = 'Password is too short';
			}else{
				if($getFromUserClass->checkEmail($email) === true){
					$error = 'Email is already in use';
				}else {
						//$getFromUserClass->register($email,$screenName,$password);
						try{
							//$getFromUserClass->create('users',array('email'=>$email,'password'=>md5($password),'screenName'=>$screeName,'profileImage'=>$profileImage,'profileCover'=>$profileCover));
							$user_id=$getFromUserClass->create('users',array('email'=>$email,'password'=>md5($password),'screenName'=>$screenName,'profileImage'=>$profileImage,'profileCover'=>$profileCover));
							$_SESSION['user_id']=$user_id;
							var_dump($user_id);
						header('Location: includes/signup.php?step=1'); 

					
						}catch(PDOException $e){
							echo 'Error Create '.$e->getMessage();
						}
					
					
				}
			}
		}
	}
}catch(PDOException $e){
	echo 'Connection Error '.$e->getMessage();
}

?>

<form method="post">
<div class="signup-div"> 
	<h3>Sign up </h3>
	<ul>
		<li>
		    <input type="text" name="screenName" placeholder="Full Name"/>
		</li>
		<li>
		    <input type="email" name="email" placeholder="Email"/>
		</li>
		<li>
			<input type="password" name="password" placeholder="Password"/>
		</li>
		<li>
			<input type="submit" name="signup" Value="Signup">
		</li>
	
		<?php if(isset($error)){
			echo ' <li class="error-li">
			<div class="span-fp-error">'.$error.'</div>
		   </li>';
		}?>
	</ul>
		
	
	
</div>
</form>