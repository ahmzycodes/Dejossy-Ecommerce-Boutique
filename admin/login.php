<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
include 'includes/head.php';

$email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
$email = trim($email);
$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
$password = trim($password);
$errors = [];

if(is_logged_in())
unset($_SESSION['DJuser']);


?>


<style>
body{
	background-image:url(/tutorial/images/biggestcop.jpg); 
	background-blend-mode: hue;
  background-color: rgba(37, 37, 34, 0.68);
  background-size: 100vw 100vh; 
  background-attachment: fixed;
  background-repeat: no-repeat;
}

.loginf{
	color: #fff;
  } 
  </style>


<div id="login-form">
 <h2 class="text-center">Login</h2><hr>

 <div>
 <?php 
  if($_POST){
    // FORM VALIDATION
    if (empty($_POST['email']) || empty($_POST['password'])) {
      $errors[] = 'You must provide both email and password';
     }
    //  Validate Email
     if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
       $errors[] ='You must enter a valid email';
     }
    //  PSSWD is more than 6 chars
     if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters'; 
     }
      //Check Email Exist In DB
      $query = $db->query("SELECT * FROM users WHERE email = '$email'");
      $user = $query->fetch_assoc();
      $userCount = $query->num_rows;
       
      if ($userCount < 1 || !password_verify($password, $user['pwd'])) {
       $errors[] = 'Username or Password is Incorrect';
      }
      if (!password_verify($password, $user['pwd'])) {
        $errors[] = 'Password Incorrect';
      }

  //check for errors 
  if (!empty($errors)) {
    echo (display_errors($errors));
  }else {
    // Log User In
    $user_id = $user['id'];
    login($user_id);
  }
 }
 
 ?>
 </div>
 <form action="login.php" method="POST">
  <div class="form-group">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" class="form-control" value="<?=$email;?>">
  </div>

  <div class="form-group">
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
  </div>
  <div class="form-group">
     <input type="submit" value="Login" class="btn btn-primary">
   </div>
 </form>
<p class="text-right"><a href="/tutorial/index.php" alt="Home">Visit Site</a></p>
</div>



<?php
include 'includes/footer.php';
?>

