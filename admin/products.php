<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

//delete products
if(isset($_GET['delete'])){
  $id=sanitize($_GET['delete']);

  $db->query("UPDATE products SET Deleted=1 WHERE ID=$id");
  header('Location: products.php');
}

$dbpath='';
$saved_image='';

if (isset($_GET['add'])|| isset($_GET['edit'])) {
  //set up variables
  $brandQuery=$db->query("SELECT * FROM Brand ORDER BY brand");
  $parentQuery=$db->query("SELECT * FROM Categories WHERE Parent=0 ORDER BY Category");
  $title=((isset($_POST['title'])&& $_POST['title'] != '')?sanitize($_POST['title']):'');
  $brand=((isset($_POST['brand'])&& !empty($_POST['brand']))?sanitize($_POST['brand']):'');
  $parent=((isset($_POST['parent'])&& !empty($_POST['parent']))?sanitize($_POST['parent']):'');
  $category=((isset($_POST['child'])&& !empty($_POST['child']))?sanitize($_POST['child']):'');

  $price=((isset($_POST['price'])&& $_POST['price'] != '')?sanitize($_POST['price']):'');
  $list_price=((isset($_POST['list_price'])&& $_POST['list_price'] != '')?sanitize($_POST['list_price']):'');
  $description=((isset($_POST['description'])&& $_POST['description'] != '')?sanitize($_POST['description']):'');
  $sizes=((isset($_POST['sizes'])&& $_POST['sizes'] != '')?sanitize($_POST['sizes']):'');
  $sizes=rtrim($sizes,',');


  if (isset($_GET['edit'])) {
    //if the page is edit do this
    $edit_id=(int)$_GET['edit'];
    $productResults=$db->query("SELECT * FROM products WHERE ID='$edit_id'");
    $product=mysqli_fetch_assoc($productResults);

    if (isset($_GET['delete_image'])) {
      $image_url=$_SERVER['DOCUMENT_ROOT'].$product['img'];echo $image_url;
      unlink($image_url);
      $db->query("UPDATE products SET img ='' WHERE ID='$edit_id'");
      header('Location: products.php?edit='.$edit_id);
    }

    //get varibles for edit page
    $category= ((isset($_POST['child'])&& $_POST['child']!='')?sanitize($_POST['child']):$product['Categories']);
    $title=((isset($_POST['title'])&& $_POST['title'] !='')?sanitize($_POST['title']):$product['Title']);
    $brand=((isset($_POST['brand'])&& $_POST['brand'] !='')?sanitize($_POST['brand']):$product['Brand']);
    $parentQ=$db->query("SELECT * FROM Categories WHERE ID='$category'");
    $parentResult=mysqli_fetch_assoc($parentQ);
    $parent=((isset($_POST['parent'])&& $_POST['parent'] !='')?sanitize($_POST['parent']):$parentResult['Parent']);

    $price=((isset($_POST['price'])&& $_POST['price'] !='')?sanitize($_POST['price']):$product['Price']);
    $price=((isset($_POST['price'])&& $_POST['price'] !='')?sanitize($_POST['price']):$product['Price']);
    $list_price=((isset($_POST['list_price'])&& $_POST['list_price'] !='')?sanitize($_POST['list_price']):$product['List_price']);
    $description=((isset($_POST['description'])&& $_POST['description'] !='')?sanitize($_POST['description']):$product['description']);
    $sizes=((isset($_POST['sizes'])&& $_POST['sizes'] !='')?sanitize($_POST['sizes']):$product['Sizes']);
    $sizes=rtrim($sizes,',');
    $saved_image=(($product['img']!='')?$product['img']:'');

    $saved_image=$saved_image;
    $dbpath=$saved_image;



  }

  if (!empty($sizes)) {
    $sizeString=sanitize($sizes);
    $sizeString=rtrim($sizeString,',');
    $sizesArray=explode(',',$sizeString);
    $sArray=array();
    $qArray=array();
    foreach ($sizesArray as $ss) {
      $s=explode(':',$ss);
      $sArray[]=$s[0];
      $qArray[]=$s[1];

    }
  }else {$sizeArray=array();}

  if ($_POST) {

    $errors=array();
    $required =array('title','brand','price','parent','child','sizes');
    foreach ($required as $field) {
      if ($_POST[$field]=='') {

        $errors[]='All fields with an astirix are required.';
        break;
      }
    }

    //check product url file is an image and nothing else
    if (!empty($_FILES)) {
    //  var_dump($_FILES);
      //array gotten from url and exploding each bit of info
      $photo=$_FILES['photo'];
      $name=$photo['name'];
      $nameArray=explode('.',$name);
      $fileName=$nameArray[0];
      $fileExt=$nameArray[1];
      $mine=explode('/',$photo['type']);
      $mineType=$mine[0];
      $mineExt=$mine[1];
      $tmpLoc=$photo['tmp_name'];
      $fileSize=$photo['size'];
      $allowed=array('png','jpg','jpeg','gif');
//path of where to put the image file
      $uploadName= md5(microtime()).'.'.$fileExt;
      $uploadPath=BASEURL.'images/products/'.$uploadName;
      $dbpath='/tutorial/images/products/'.$uploadName;
      if ($mineType!='image') {
        $errors[]='The file must be an image.';
      }
      if (!in_array($fileExt,$allowed)) {
        $error[]='The photo must be a png, jpg, jpeg or gif.';
      }
      if ($fileSize>15000000) {
        $error[]='the file size must be under 15mb';
      }
      if ($fileExt!=$mineExt && ($mineExt=='jpeg'&& $fileExt!='jpg') ) {
        $error[]='File extension does not match the file';
      }

    }
    //check if error
    if (!empty($errors)) {
      echo display_errors($errors);
    }else{
      //update database
      if(!empty($_FILES)){
        move_uploaded_file($tmpLoc,$uploadPath);
      }

      $insertSql="INSERT INTO products (Title,Price,List_price,Brand,Categories,Sizes,img,description)
      VALUES ('$title','$price','$list_price','$brand','$category','$sizes','$dbpath','$description')";

      if(isset($_GET['edit'])){

        $insertSql="UPDATE products SET Title ='$title', Price ='$price', List_price ='$list_price',
        Brand ='$brand', Categories ='$category', Sizes ='$sizes', img ='$dbpath', description ='$description' WHERE ID ='$edit_id'";
      }

      $db->query($insertSql);

      header('Location:products.php');

  }}
  ?>

  <h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add a new');?> product</h2>
  <form class="" action="products.php?<?=((isset($_GET['edit'])?'edit='.$edit_id:'add=1'))?>" method="POST" enctype="multipart/form-data">
    <div class="row">
    <div class="form-group col-sm-3">
      <label for="title">Title*:</label>
      <input type="text" name="title" class="form-control" id="title" value="<?=$title?>">
    </div>
    <div class="form-group col-sm-3">
      <label for="brand">Brand*:</label>
      <select class="form-control" id="brand" name="brand">
        <option value=""<?=(($brand=='')?' selected':'');?>></option>
          <?php while($b=mysqli_fetch_assoc($brandQuery)):?>
            <option value="<?=$b['id'];?>"<?=(($brand==$b['id'])?' selected':'');?>><?=$b['brand'];?></option>
            <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group col-sm-3">
      <label for="parent">Parent Category*:</label>
      <select class="form-control" id="parent" name="parent">
        <option value=""<?=(($parent=='')?' selected':'');?>></option>
        <?php while($p=mysqli_fetch_assoc($parentQuery)):?>
          <option value="<?=$p['ID'];?>"<?=(($parent==$p['ID'])?' selected':'');?>><?=$p['Category'];?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group col-sm-3">
      <label for="child">Child Category*:</label>
      <select id="child"class="form-control" name="child">
      </select>
    </div>

    <div class="form-group col-sm-3">
      <label for="price">Price*:</label>
      <input name="price" id="price"class="form-control" value="<?=$price?>" type="text">
    </div>

    <div class="form-group col-sm-3">
      <label for="list_price">List Price:</label>
      <input name="list_price" id="list_price"class="form-control" value="<?=$list_price?>" type="text">
    </div>
    <div class="form-group col-sm-3">
      <label>Quantity & Sizes</label>
      <button  class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
    </div>
    <div class="form-group col-sm-3">
      <label for="sizes">Sizes $ quantity preview</label>
      <input type="text" class="form-control" name="sizes" id="sizes" value="<?=$sizes;?>" readonly>
    </div>
    <div class="form-group col-sm-6">
      <?php if ($saved_image!=''): ?>
        <div class="save-image">
          <img src="<?=$saved_image;?>" alt="saved iamge"><br>
          <a href="products.php?delete_image=1&edit=<?=$edit_id?>" class="text-danger">Delete Image</a>
        </div>
      <?php else: ?>
      <label for="photo">Product Photo:</label>
      <input type="file" class="form-control" name="photo" id="photo" >
    <?php endif; ?>
    </div>
    <div class="form-group col-sm-6">
      <label for="description">Description:</label>
      <textarea type="text" class="form-control" name="description" id="descriptio" rows="6"><?=$description?></textarea>
    </div>

</div>
<div class="form-group float-right" >
  <a href="products.php" class="btn btn-default "> Cancel</a>
  <input type="submit" value="<?=((isset($_GET['edit']))?'Edit ':'Add ');?> Product" class="btn btn-success">
</div><div class="clearfix"></div>

  </form>
  <!-- Modal -->
  <div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Sizes</h4>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">

        <div class="row">


        <?php for ($i=1; $i <= 12 ; $i++):?>

          <div class="form-group col-sm-4">

            <label for="size<?=$i;?>">Size</label>

            <input type="text"  name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:'');?>" class="form-control">
          </div>
          <div class="form-group col-sm-2">
            <label for="quantity<?=$i;?>">Quantity</label>
            <input type="number" name="quantity<?=$i;?>" id="quantity<?=$i;?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:'');?>" min="0" class="form-control">
          </div>

        <?php endfor; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updateSizes(); jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
      </div>
    </div>
  </div>
</div>

<?php }

else{
$sql="SELECT * FROM products WHERE deleted!=1";
$presults=$db->query($sql);
if (isset($_GET['featured'])) {
  $id=(int)$_GET['id'];
  $featured=(int)$_GET['featured'];
  $featuresql="UPDATE products SET Featured='$featured' WHERE ID='$id'";
  $db->query($featuresql);
  header('Location: products.php');
  # code...
}
 ?>

<div class="row">

<div class="col-sm-12">


<h2 class="text-center">Products</h2>
</div>
<div class="col-sm-12">
  <br>
<a href="products.php?add=1" class="btn btn-success" id="add-product-btn" style="margin-top:-35px; margin-right:2%; float:right;">Add Product</a><div class="clearfix">
<a href="archiveProduct.php?add=1" class="btn" id="archive-product-btn"  style="margin-top:-35px; margin-right:2%; float:right;">Archive</a>
</div>
</div>

</div>

<hr>

<table class="table table-bordered table-condesed table-striped">
  <thead>
    <th></th>
    <th></th>
    <th>Product</th>
    <th>Price</th>
    <th>Category</th>
    <th>Feature</th>
    <th>Sold</th>
  </thead>
  <tbody>
    <?php while($product=mysqli_fetch_assoc($presults)):
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
          <a href="products.php?edit=<?=$product['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/file.svg"></a>
        </td>
        <td>
          <a href="products.php?delete=<?=$product['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/x.svg"></a>
        </td>
        <td><?=$product['Title'];?></td>
        <td><?=money($product['Price']);?></td>
        <td><?=$category;?></td>
        <td><a href="products.php?featured=<?=(($product['Featured']==0)?'1':'0');?>&id=<?=$product['ID'];?>" class="btn btn-xs btn-default"><img src="../iconic/svg/<?=(($product['Featured']==1)?'cog':'crop');?>.svg">
        </a> <?=(($product['Featured']==1)?'Featured Product':'');?></td>
        <td>0</td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>




<?php
}
include 'includes/footer.php'; ?>

 <script>
  $('document').ready(function(){
    get_child_option('<?=$category;?>');
  });

 </script>
