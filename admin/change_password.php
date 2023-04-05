<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if (!is_logged_in()) {
    login_error_redirect();
}
include 'includes/head.php';
$hashed  = $user_data['pwd'];
$old_password = ((isset($_POST['old_password']))?sanitize($_POST['old_password']):'');
$old_password = trim($old_password);
$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
$password = trim($password);
$confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
$confirm = trim($confirm);
$new_hashed = password_hash($password, PASSWORD_DEFAULT);
$user_id = $user_data['id'];
$errors = [];


?>
<div id="login-form">
 <h2 class="text-center">Change Password</h2><hr>

 <div>
 <?php 
  if($_POST){
    // FORM VALIDATION
    if (empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])) {
      $errors[] = 'You must fill out <strong>all fields</strong>';
     }

    //  PSSWD is more than 6 chars
     if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters'; 
     }
        
      if (!password_verify($old_password, $hashed)) {
        $errors[] = 'Old password is incorrect';
      }
      if ($password!==$confirm) {
          $errors[] = 'New passwords do not Match';
      }

  //check for errors 
  if (!empty($errors)) {
    echo (display_errors($errors));
  }else {
    //Change Pwd
    $db->query("UPDATE users SET pwd ='$new_hashed' WHERE id = '$user_id'");
    $_SESSION['success_flash'] = 'New Password Updated!';
    header('Location: index.php');
  }
 }
 
 ?>
 </div>
 <form action="change_password.php" method="POST">
  <div class="form-group">
    <label for="old_password">Old Pasword:</label>
    <input type="password" name="old_password" id="old_password" class="form-control" value="<?=$old_password;?>">
  </div>

  <div class="form-group">
    <label for="password">New Password:</label>
    <input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
  </div>
  <div class="form-group">
    <label for="confirm">Confirm New Password:</label>
    <input type="password" name="confirm" id="confirm" class="form-control" value="<?=$confirm;?>">
  </div>
  <div class="form-group">
  <a href="index.php" class="btn btn-default">Cancel</a>
     <input type="submit" value="Change" class="btn btn-primary">
   </div>
 </form>
<p class="text-right"><a href="/tutorial/index.php" alt="Home">Visit Site</a></p>
</div>



<?php
include 'includes/footer.php';
?>

