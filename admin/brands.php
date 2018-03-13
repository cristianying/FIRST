<?php
require_once '../core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
//get brands from database
$sql = "SELECT * FROM Brand ORDER BY brand";
$results=$db->query($sql);
$errors=array();

//edit brands
if (isset($_GET['edit'])&&!empty($_GET['edit'])) {
  $edit_id=(int)$_GET['edit'];
  $edit_id=sanitize($edit_id);
  $sql2="SELECT * FROM Brand WHERE id='$edit_id'";
  $edit_result=$db->query($sql2);
  $eBrand=mysqli_fetch_assoc($edit_result);

  //printing deleted id item
  //echo $edit_id;
  // $sql="EDIT FROM Brand WHERE id='$edit_id'";
  // $db->query($sql);

}

//delete brands
if (isset($_GET['delete'])&&!empty($_GET['delete'])) {
  $delete_id=(int)$_GET['delete'];
  $delete_id=sanitize($delete_id);
  //printing deleted id item
  //echo $delete_id;
  $sql="DELETE FROM Brand WHERE id='$delete_id'";
  $db->query($sql);
  header('Location: brands.php');
}

//if add form is submited
if(isset($_POST['add_submit'])){
  $brand=sanitize($_POST['brand']);
  //check if brand is blank
  if($_POST['brand']==''){
    $errors[] .='you must enter a brand';
  }
  //check if brand exists in Database
  $sql="SELECT * FROM Brand WHERE brand='$brand'";
if(isset($_GET['edit'])){
  $sql="SELECT * FROM Brand WHERE brand='$brand' AND id !='$edit_id'";
}
  $result=$db->query($sql);
  $count = mysqli_num_rows($result);
  if($count>0){
    $errors[] .= $brand.' already exists. Please choose another brand name...';
  }
  //how many have the same brand name
  //echo $count;
  //display errors
  if(!empty($errors)){
    echo display_errors($errors);
  }else{
    // add brand to Database
    $sql="INSERT INTO Brand (brand) VALUES ('$brand')";
    if(isset($_GET['edit'])){
      $sql="UPDATE Brand SET brand='$brand' WHERE id='$edit_id'";
    }
    $db->query($sql);
    header('Location: brands.php');
  }
}
?>
<h2 class="text-center">Brands</h2>
<hr>
<!--brand form-->
<div class="container h-100">
<div class="row h-100 justify-content-center align-items-center" >
  <form class="form-inline" action="brands.php<?=((isset($_GET['edit']))?'?edit='.$edit_id:'');?>" method="post">
    <div class="form-group" >
      <?php
      $brand_value='';
      if(isset($_GET['edit'])){
        $brand_value = $eBrand['brand'];
      }else{
        if(isset($_POST['brand'])){
          $brand_value=sanitize($_POST['brand']);
        }
      } ?>
      <label for="brand"><?=((isset($_GET['edit']))?'Edit ':'Add a ');?>Brand:</label>
      <input type="text" name="brand" id="brand" class="form-control" value="<?=$brand_value;?>">
      <?php if(isset($_GET['edit'])): ?>
        <a href="brands.php" class="btn btn-default">cancel</a>
      <?php endif; ?>
      <input type="submit" name="add_submit" value="<?=((isset($_GET['edit']))?'Edit ':'Add');?> Brand" class="btn btn-large btn-success">
    </div>
  </form>
</div>
</div><hr>

 <table id="brandtable" style="" class="table table-bordered table-striped table-condensed" >
  <thead>
    <th></th>
    <th>Brand</th>
    <th></th>
  </thead>
  <tbody>
    <?php while($brand=mysqli_fetch_assoc($results)): ?>
    <tr>
      <td><a href="brands.php?edit=<?= $brand['id'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/file.svg"></a></td>
      <td><?= $brand['brand'];?></td>
      <td><a href="brands.php?delete=<?= $brand['id'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/x.svg"></a></td>
    </tr>
  <?php endwhile;?>
  </tbody>
</table>
<?php
  include 'includes/footer.php';
 ?>
