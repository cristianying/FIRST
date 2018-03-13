<?php
require_once '../core/init.php';
//used to check if user checked in or not
if(!is_logged_in()){
  login_error_redirect();
}
//check if users is admin or not
if (!has_permission('admin')) {
  permission_error_redirect('index.php');
}
include 'includes/head.php';
include 'includes/navigation.php';

if(isset($_GET['delete'])){
  $delete_id=sanitize($_GET['delete']);
  $db->query("DELETE FROM Users WHERE ID='$delete_id'");
  $_SESSION['success_flash']=' User has been deleted';
  header('Location: users.php');
}
if (isset($_GET['add'])|| isset($_GET['edit']) ) {
  $name=((isset($_POST['name']))?sanitize($_POST['name']):'');
  $email=((isset($_POST['email']))?sanitize($_POST['email']):'');
  $password=((isset($_POST['password']))?sanitize($_POST['password']):'');
  $confirm=((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
  $permissions=((isset($_POST['permissions']))?sanitize($_POST['permissions']):'');
  $errors=array();

  if ($_POST) {
    $emailQuery=$db->query("SELECT * FROM Users WHERE Email='$email'");
    $emailCount=mysqli_num_rows($emailQuery);
if(isset($_GET['add'])){
    if ($emailCount!=0) {
      $errors[]='That email already exists';
    }

    //array names from form below

    $required=array('name','email','password','confirm','permissions');
    foreach ($required as $f) {
      if (empty($_POST[$f])) {
        $errors[]='you must fill out all fields';
        break;
      }
    }

    if (strlen($password)<6) {
      $errors[]='password must be at leats 6 characters';
    }
    if ($password!=$confirm) {
      $errors[]='Passwords do not match';
    }
    }

    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
      $errors[]='you must enter a valid email';
    }

    if(!empty($errors)){
      echo display_errors($errors);
    }else if(isset($_GET['add'])){
      //add users after all error checks
      $hashed=password_hash($password,PASSWORD_DEFAULT);
      $db->query("INSERT INTO Users (Full_name,Email,Password,Permissions) values ('$name','$email','$hashed','$permissions')");
      $_SESSION['success_flash']='User has been added';
      header('Location: users.php');
    }else{
      $edit_id=$_GET['edit'];
      $db->query("UPDATE Users SET Full_name='$name', Email='$email', Permissions='$permissions' WHERE ID='$edit_id'");
      $_SESSION['success_flash']='User has been edited';
      header('Location: users.php');
    }
  }

  if (isset($_GET['edit'])) {
    $edit_id=$_GET['edit'];
    $editQuery=$db->query("SELECT * FROM Users WHERE ID='$edit_id'");
    $editInfo=mysqli_fetch_assoc($editQuery);


  }

  ?>
<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit ':'Add a new ')?> User</h2><hr>
<form class="" action="users.php?<?=((isset($_GET['edit']))?'edit='.$_GET['edit']:'add=1')?>" method="post">
  <div class="row" style="padding: 0px 10px;">
  <div class="form-group col-sm-6">
    <label for="name">Full name</label>
    <input type="text" name="name" id="name" class="form-control" value="<?=((isset($_GET['edit']))?$editInfo['Full_name']:$name);?>">
  </div>
  <div class="form-group col-sm-6">
    <label for="email">Email</label>
    <input type="text" name="email" id="email" class="form-control" value="<?=((isset($_GET['edit']))?$editInfo['Email']:$email);?>">
  </div>
  <?php if (isset($_GET['add'])): ?>
  <div class="form-group col-sm-6">
    <label for="password">Password</label>
    <input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
  </div>
  <div class="form-group col-sm-6">
    <label for="confirm">Confirm password</label>
    <input type="password" name="confirm" id="confirm" class="form-control" value="<?=$confirm;?>">
  </div>
<?php endif; ?>
  <div class="form-group col-sm-6">
    <label for="permissions">Permissions</label>
    <select class="form-control" name="permissions">
      <option value=""<?=(($permissions=='')?' selected':'')?>></option>
      <option value="editor"<?=(($permissions=='editor'||$editInfo['Permissions']=='editor')?' selected':'')?>>Editor</option>
      <option value="admin,editor"<?=(($permissions||$editInfo['Permissions']=='admin,editor')?' selected':'')?>>Admin</option>
    </select>
  </div>
  <div class="form-group col-sm-6 text-right" style="margin-top: 25px">
    <a href="users.php" class="btn btn-default">Cancel</a>
    <input type="submit" value="<?=((isset($_GET['edit']))?'Confirm Edit':'Add User')?> " class="btn btn-primary">
  </div>
  </div>
</form>

  <?php
}else{

$userQuery=$db->query("SELECT * FROM Users ORDER BY Full_name");
?>
<h2>Users</h2><hr>
<a href="users.php?add=1" class="btn btn-success pull-right" id="add-product-btn" style="margin-top:-10px;margin-bottom:5px; margin-right:2%; float:right;">Add New User</a>
<div class="clearfix"></div>
<table class="table table-bordered table-striped table-condensed">
  <thead>
    <th>delete</th>
    <th>edit</th>
    <th>Name</th>
    <th>Email</th>
    <th>Join Date</th>
    <th>Last login</th>
    <th>Persmissions</th>
  </thead>
  <tbody>
    <?php while($user=mysqli_fetch_assoc($userQuery)): ?>
    <tr>
      <td>
        <?php if($user['ID'] != $user_data['ID']):?>
          <a href="users.php?delete=<?=$user['ID'];?>" class="btn btn-default btn-xs">
            <img src="../iconic/svg/x.svg">
          </a>
        <?php endif; ?>

      </td>
      <td>
        <?php if($user['ID'] != $user_data['ID']):?>
          <a href="users.php?edit=<?=$user['ID'];?>" class="btn btn-default btn-xs">
            <img src="../iconic/svg/file.svg">
          </a>
        <?php endif; ?>
      </td>
      <td><?=$user['Full_name'];?></td>
      <td><?=$user['Email'];?></td>
      <td><?=pretty_date($user['Join_date']);?></td>
      <td><?=(($user['Last_login']=='0000-00-00 00:00:00')?'Never': pretty_date($user['Last_login']));?></td>
      <td><?=$user['Permissions'];?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>

</table>
<?php
}
  include 'includes/footer.php';
 ?>
