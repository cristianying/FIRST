<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';

//parentID is gotten from the post method in footer
//$this $_POST is getting the parentID passed from footer ajax.
$parentID=(int)$_POST['parentID'];
$selected=sanitize($_POST['selected']);
$childQuery=$db->query("SELECT * FROM Categories WHERE Parent='$parentID' ORDER BY Category");
//pre build php to start buffering
ob_start();
 ?>

<option value=""></option>
<?php while($child=mysqli_fetch_assoc($childQuery)): ?>
<option value="<?=$child['ID'];?>"<?=(($selected==$child['ID'])?'selected':'')?>><?=$child['Category'];?></option>
<?php endwhile; ?>

 <?php
//out put html echo the buffer back and release the memory
//echo back to the ajax request in footer as 'data' in the success  function
 echo ob_get_clean(); ?>
