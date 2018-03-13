<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
include 'includes/head.php';

include 'includes/navigation.php';
$sql="SELECT * FROM Categories WHERE Parent=0";
$result=$db->query($sql);

$errors=array();
$category='';
$post_parent='';

// edit Category
if(isset($_GET['edit'])&& !empty($_GET['edit'])){
  $edit_id=(int)$_GET['edit'];
  $edit_id=sanitize($edit_id);
  $edit_sql="SELECT * FROM Categories WHERE id='$edit_id' ";
  $edit_result=$db->query($edit_sql);
  $edit_category=mysqli_fetch_assoc($edit_result);
}
// delete Category

if (isset($_GET['delete'])&& !empty($_GET['delete'])) {
  $delete_id= (int)$_GET['delete'];
  $delete_id=sanitize($delete_id);
  //check if parent category has child
  $ccsql="SELECT * FROM Categories WHERE ID='$delete_id'";
  $cparent=$db->query($ccsql);
  $cparent=mysqli_fetch_assoc($cparent);

  if($cparent['Parent']=="0"){
    $id=$cparent['ID'];
    $chlchk = $db->query("SELECT * FROM Categories WHERE Parent = '$id'");
    $count = mysqli_num_rows($chlchk);
    if ($count == 1) {
       $errors[] .= 'This category has 1 children, please delete this first';
     }else if($count > 1) {
       $errors[] .= 'This category has '.$count.' children, please delete these first';

     }
       else {
       // hasn't got children
       $db->query("DELETE FROM Categories WHERE ID = '$delete_id'");
      }
     } else {
      // is not a parent category
      $db->query("DELETE FROM Categories WHERE ID = '$delete_id'");
     }
     if (!empty($errors)) {
      // display errors
      echo display_errors($errors);
     } else {
      header('Location: categories.php');
     }

}

//Process form
if (isset($_POST)&&!empty($_POST)) {
  $post_parent=sanitize($_POST['parent']);
  $category=sanitize($_POST['category']);
  $sqlform= "SELECT * FROM Categories WHERE Category ='$category' AND Parent='$post_parent'";
  if (isset($_GET['edit'])) {
      $id=$edit_category['ID'];
    $sqlform="SELECT * FROM Categories WHERE Category= '$category' AND parent='$post_parent' AND ID!='$id'";
  }
  $fresult=$db->query($sqlform);
  $count=mysqli_num_rows($fresult);

  //if category is blank
  if ($category=='') {
    $errors[].='The cateogry cannot be left blank.';
    # code...
  }
  //if exists in the Database
  if ($count>0) {
    $errors[].=$category.' already exists. Please choose a new category.';
  }

  //display errors or update Database
  if(!empty($errors)){
    //display errors
    $display=display_errors($errors); ?>
<script>
jQuery('document').ready(function(){
  jQuery('#errors').html('<?=$display;?>');
});

</script>
  <?php }else{
    //update database
    $updatesql="INSERT INTO Categories (Category,Parent) VALUES ('$category','$post_parent')";
    if(isset($_GET['edit'])){
      $updatesql="UPDATE Categories SET Category='$category',Parent='$post_parent' WHERE ID='$edit_id'";
    }
    $db->query($updatesql);
    header('Location:categories.php');

  }
}
//category input value set up
$category_value='';
$parent_value=0;
if(isset($_GET['edit'])){
  $category_value=$edit_category['Category'];
  $parent_value=$edit_category['Parent'];
}else{
  if (isset($_POST)) {
    $category_value=$category;
    $parent_value=$post_parent;
  }
}

 ?>

<h2 class"text-center">Categories</h2><hr>
<div class="row">

  <!--Form-->
  <div class="col-md-6">
    <form class="form" action="categories.php<?=((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
      <legend><?=((isset($_GET['edit']))?'Edit ':'Add a ');?> Category</legend>
      <div id="errors">

      </div>
      <div class="form-group">
        <label for="parent">Parent</label>
        <select class="form-control" name="parent" id="parent">
          <option value="0" <?=(($parent_value==0)?'selected="selected"':'');?>>Parent</option>
          <?php while($parent=mysqli_fetch_assoc($result)) :?>
            <option value="<?=$parent['ID'];?>"<?=(($parent_value==$parent['ID'])?'selected="selected"':'');?>><?=$parent['Category'];?></option>
        <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="category">Category</lable>
          <input type="text" class="form-control" name="category" id="category" value="<?=$category_value;?>" >
      </div>
      <div class="form-group">
        <?php if(isset($_GET['edit'])): ?>
          <a href="categories.php" class="btn btn-default">cancel</a>
        <?php endif; ?>
        <input class="btn btn-success" type="submit"  value="<?=((isset($_GET['edit']))?'edit ':'Add ');?> Category">
      </div>
    </form>
  </div>
  <!--category table-->

  <div class="col-md-6">
    <table class="table table-bordered">
      <thead>
        <th>Category</th>
        <th>Parent</th>
        <th></th>
        <tbody>

          <?php
          $sql="SELECT * FROM Categories WHERE Parent=0";
          $result=$db->query($sql);
          while($parent = mysqli_fetch_assoc($result)):
            $parent_id=(int)$parent['ID'];
            $sql2="SELECT * FROM Categories WHERE Parent='$parent_id'";
            $cresult=$db->query($sql2);

            ?>
          <tr class="bg-primary">
            <td><?=$parent['Category'];?></td>
            <td>Parent</td>
            <td>
              <a href="categories.php?edit=<?=$parent['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/file.svg"></a>
              <a href="categories.php?delete=<?=$parent['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/x.svg"></a>
            </td>
          </tr>
          <?php while($child=mysqli_fetch_assoc($cresult)): ?>
            <tr class="bg-info">
              <td><?=$child['Category'];?></td>
              <td><?=$parent['Category'];?></td>
              <td>
                <a href="categories.php?edit=<?=$child['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/file.svg"></a>
                <a href="categories.php?delete=<?=$child['ID'];?>" class="btn btn-xs btn-default" ><img src="../iconic/svg/x.svg"></a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endwhile; ?>
        </tbody>
      </thead>
    </table>
  </div>
</div>

<?php
include 'includes/footer.php';
 ?>
