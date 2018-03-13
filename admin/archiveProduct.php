<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

$productResults=$db->query("SELECT * FROM products WHERE Deleted=1");

if(isset($_GET['undeleted'])){

  $id=sanitize($_GET['undeleted']);

  $db->query("UPDATE products SET Deleted=0 WHERE ID=$id");
header('Location: archiveProduct.php');
}

 ?>
 <div class="row">
 <div class="col-sm-12">
 <h2 class="text-center">Products</h2>
 </div>
 <div class="col-sm-12">
   <br>
 <a href="products.php" class="btn btn-success" id="add-product-btn" style="margin-top:-35px; margin-right:2%; float:right;">Back to Products</a><div class="clearfix">
 </div>
 </div>
 </div>
 <hr>

 <table class="table table-bordered table-condesed table-striped">
   <thead>
     <th> undelete</th>
     <th>Product</th>
     <th>Price</th>
     <th>Category</th>
   </thead>
   <tbody>
     <?php while($product=mysqli_fetch_assoc($productResults)):
       $childID=$product['Categories'];
       $catSql="SELECT * FROM Categories WHERE ID='$childID'";
       $result=$db->query($catSql);
       $child=mysqli_fetch_assoc($result);
       $parentID=$child['Parent'];
       $pSql="SELECT * FROM Categories WHERE ID='$parentID'";
       $presult=$db->query($pSql);
       $parent=mysqli_fetch_assoc($presult);
       $category=$parent['Category'].'~'.$child['Category'];
       ?>
       <tr>
         <td>
           <a href="archiveProduct.php?undeleted=<?=$product['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/action-redo.svg"></a>
         </td>
         <td><?=$product['Title'];?></td>
         <td><?=money($product['Price']);?></td>
         <td><?=$category;?></td>
       </tr>
     <?php endwhile; ?>
   </tbody>
   </table>

 <?php

 include 'includes/footer.php'; ?>
