<?php
require_once '../core/init.php';
if (!is_logged_in()) {
    login_error_redirect();
}
 if (!has_permission('admin')) {
     permission_error_redirect('index.php');
 }
include 'includes/head.php';
include 'includes/navigation.php';
if (isset($_GET['delete'])) {
    $delete_id = sanitize($_GET['delete']);
    $db->query("DELETE FROM users WHERE id ='$delete_id'");
    $_SESSION['success_flash'] = 'User has been deleted';
    header('Location: users.php');}
    if (isset($_GET['add']) || isset($_GET['edit'])) {
        $name = ((isset($_POST['name']))?sanitize($_POST['name']):'');
        $email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
        $password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
        $confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
        $permissions = ((isset($_POST['permissions']))?sanitize($_POST['permissions']):'');
        $errors = [];
        if (isset($_GET['edit'])) {
          $edit_id = (int)$_GET['edit'];
          $userRes = $db->query("SELECT * FROM users WHERE id = '$edit_id'"); 
          $user = $userRes->fetch_assoc();
        $name = ((isset($_POST['name']))?sanitize($_POST['name']):$user['full_name']);
        $email = ((isset($_POST['email']))?sanitize($_POST['email']):$user['email']);
        $password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
        $confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
        $permissions = ((isset($_POST['permissions']))?sanitize($_POST['permissions']):$user['permissions']);
        }
        if ($_POST) {
  
          $emailQuery = $db->query("SELECT * FROM users WHERE email = '$email'");
          if(isset($_GET['edit'])){
            $emailQuery = $db->query("SELECT * FROM users WHERE email ='$email' AND id !='$edit_id'");
          }
          $emailCount = $emailQuery->num_rows;
          
          if ($emailCount !== 0) {
            $errors[] = 'Email already exists';
          }
        if(!isset($_GET['edit'])){
          $required = array('name','email','password','confirm','permissions');
         foreach ($required as $f) {
           if (empty($_POST[$f])) {
             $errors[] = 'You must fill out all <b>fields</b>';
             break; }
         }
        }
        if(isset($_GET['edit'])){
          if(empty($name) || empty($permissions)){
          $errors[] = 'You must fill out all <b>Fields</b> except <b>Passwords</b> ';
        }
        }
           pwd_check($password);
           
          if($password !== $confirm){
            $errors[] = 'Passwords do not match';
          }
          if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'You must enter a valid email';
          }

          if(strpos(trim($name), " ") === false)
            $errors[] = '*Surname is Required';



         if (!empty($errors)) {
           echo display_errors($errors);
         }else {
           if (!empty($password) && $password !='') {
            $hashed = password_hash($password,PASSWORD_DEFAULT);
           }
          //  Edit: Update Db
            if (isset($_GET['edit'])) {
              $edit_query = "UPDATE users SET full_name='$name',email='$email',pwd='$hashed',permissions='$permissions' WHERE id =$edit_id";
              if (!isset($hashed)) {
              $edit_query = "UPDATE users SET full_name='$name',email='$email',permissions='$permissions' WHERE id =$edit_id";
             }
              $db->query($edit_query);
              $_SESSION['success_flash'] = 'User data updated successfully';
              header('Location: users.php');
              echo(gettype($password));
            }else{
          
            // Add user to database
            $db->query("INSERT INTO users (full_name,email,pwd,permissions) VALUES ('$name','$email','$hashed','$permissions')");
            $_SESSION['success_flash'] = 'New user addedd successfully';
            header('Location: users.php');
            }
         }
        }



        
        ?>
    <h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add A New'); ?> User</h2><hr>

    <form action="users.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1'); ?>" method="POST">
     <div class="form-group col-md-6">
        <label for="name">Full Name:</label>
        <input type="text" name='name' id="name" class="form-control" value="<?=$name;?>">
     </div>
     <div class="form-group col-md-6">
        <label for="email">Email:</label>
        <input type="email" name='email' id="email" class="form-control" value="<?=$email;?>">
     </div>
     <div class="form-group col-md-6">
        <label for="password">Password:</label>
        <input type="password" name='password' id="password" class="form-control" value="<?=$password;?>">
     </div>
     <div class="form-group col-md-6">
        <label for="confirm">Confirm Password:</label>
        <input type="password" name='confirm' id="confirm" class="form-control" value="<?=$confirm;?>">
     </div>
     <div class="form-group col-md-6">
        <label for="permissions">Permissions:</label>
          <select name="permissions" class="form-control" id="permission">
              <option value=""<?=(($permissions == '')?'selected':''); ?>></option>
              <option value="editor"<?=(($permissions == 'editor')?'selected':''); ?>>Editor</option>
              <option value="admin,editor"<?=(($permissions == 'admin,editor')?'selected':''); ?>>Admin (All Privileges)</option>     
        </select>
     </div>
     <div class="form-group col-md-6 text-right" style="margin-top: 25px;">
      <a href="users.php" class="btn btn-default">Cancel</a>
      <input type="submit" value="<?=((isset($_GET['edit']))?'Edit':'Add');?> User" class="btn btn-primary">
     </div> 

    
    </form>




 <?php   }else {    
$userQuery = $db->query("SELECT * FROM users ORDER BY full_name");

?>

<h2 class="text-center">Users</h2>
<a href="users.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add New User</a>
<hr>
<table class="table table-bordered table-striped table-condensed">
<thead>
<th></th>
<th>Name</th>
<th>Email</th>
<th>Join Date</th>
<th>Last Login</th>
<th>Permissions</th>
</thead>

<tbody>
<?php while ($user = $userQuery->fetch_assoc()): ?>
    <tr>
    <td>
      <?php if($user['id'] != $user_data['id']): ?>
      <a href="users.php?edit=<?=$user['id'];?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
      <a href="users.php?delete=<?=$user['id'];?>" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span></a>
      <?php endif; ?>
    </td>
    <td><?=$user['full_name'];?></td>
    <td><?=$user['email'];?></td>
    <td><?=pretty_date($user['join_date']);?></td>
    <td><?=(($user['last_login'] == '0000-00-00 00:00:00')?'Never':pretty_date($user['last_login']));?></td>
    <td><?=$user['permissions'];?></td>
    </tr>
   <?php endwhile; ?>
  </tbody>
</table>

    <?php }
include 'includes/footer.php';
?>