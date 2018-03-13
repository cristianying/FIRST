<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
include 'includes/head.php';


$email=((isset($_POST['email']))?sanitize($_POST['email']):'');
$email=trim($email);
$password=((isset($_POST['password']))?sanitize($_POST['password']):'');
$password=trim($password);
$errors=array();
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
      if(empty($_POST['email']) || empty($_POST['password'])) {
        $errors[]='you must enter email and password';
      }

      //validate Email
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[]='you must enter a valid email';
        # code...
      }
      //password is more than 6 characters
      if (strlen($password)<6) {
        $errors[]='Password must be at least 6 characters.';
      }

      //check if email exists in the database
      $query=$db->query("SELECT * FROM Users WHERE Email='$email'");
      $user=mysqli_fetch_assoc($query);
      $userCount=mysqli_num_rows($query);

      if($userCount<1){
        $errors[]='That email does not exist';
      }
      // password_verify pre build php function goes hand
      //in hand with password_hash up in the file. two paramenters, first is
      //entered password and match it
      if (!password_verify($password,$user['Password'])) {
        $errors[]='password does not match.';
      }

      if (!empty($errors)) {
        echo display_errors($errors);
      }else{
        //log user in after all validation passed

        //check at helper.php and then go index and then init to check
        $user_id=$user['ID'];
        login($user_id);
      }

    } ?>


  </div>
    <h2 class="text-center">login</h2><hr>
    <form class="" action="login.php" method="post">
      <div class="form-group">
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" value="<?=$email;?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" value="<?=$password;?>" class="form-control">
      </div>
      <div class="form-group">
        <input type="submit" name="" value="login" class="btn btn-primary">
      </div>
    </form>
    <p class="text-right"><a href="/tutorial/index.php" alt="home"> Visit Site</a></p>

</div>


<?php
include 'includes/footer.php';
 ?>
