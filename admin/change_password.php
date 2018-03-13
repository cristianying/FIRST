<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
include 'includes/head.php';

$hashed=$user_data['Password'];
$old_password=((isset($_POST['old_password']))?sanitize($_POST['old_password']):'');
$old_password=trim($old_password);
$password=((isset($_POST['password']))?sanitize($_POST['password']):'');
$password=trim($password);
$confirm=((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
$confirm=trim($confirm);
$new_hashed=password_hash($password, PASSWORD_DEFAULT);
$errors=array();
$user_id=$user_data['ID'];
?>
<style>
  body{
    background-image: url("/tutorial/images/headerlogo/background.png");
    background-size: 100vw 100vh;
    background-attachment: fixed;
  }
</style>
<div id="login-form" class="">
  <div>
    <?php if($_POST){
      //form validation
      if(empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])) {
        $errors[]='must fill out all fields';
      }



      //password is more than 6 characters
      if (strlen($password)<6) {
        $errors[]='Password must be at least 6 characters.';
      }
      //if new password matches confirms
      if($password!=$confirm){
        $errors[]='The new password and confirm does not match';
      }

      // password_verify pre build php function goes hand
      //in hand with password_hash up in the file. two paramenters, first is
      //entered password and match it
      if (!password_verify($old_password,$hashed)) {
        $errors[]='your old password does not match our records';
      }

      if (!empty($errors)) {
        echo display_errors($errors);
      }else{
        //$2y$10$PpQC1MZ4Ijt2jf1JfffrlOPjYg2kED7rIHykKKajZma8x29dWXFjS
        //$2y$10$vRK6iaO3ZAkGaK8aY92Dq.v23UQo/BecAUobdTV.1.nn.U3S.X0rm

      //change password

      $db->query("UPDATE Users SET Password='$new_hashed' WHERE ID='$user_id'");
      $_SESSION['success_flash']='your password has been updated';
      header('Location: index.php');
      }

    } ?>


  </div>
    <h2 class="text-center">Change Password</h2><hr>
    <form class="" action="change_password.php" method="post">
      <div class="form-group">
        <label for="old_password">Old Password: </label>
        <input type="password" name="old_password" id="old_password" value="<?=$old_password;?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="password">New Password: </label>
        <input type="password" name="password" id="password" value="<?=$password;?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="confirm">Confirm New Password: </label>
        <input type="password" name="confirm" id="confirm" value="<?=$confirm;?>" class="form-control">
      </div>
      <div class="form-group">
        <a href="index.php" class="btn btn-default">Cancel</a>
        <input type="submit" name="" value="login" class="btn btn-primary">
      </div>
    </form>
    <p class="text-right"><a href="/tutorial/index.php" alt="home"> Visit Site</a></p>

</div>


<?php
include 'includes/footer.php';
 ?>
