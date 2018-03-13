<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
  $mode=sanitize($_POST['mode']);
  $edit_size=sanitize($_POST['edit_size']);
  $edit_id=sanitize($_POST['edit_id']);
  $cartQ=$db->query("SELECT * FROM Cart WHERE ID='{$cart_id}'");
  $result=mysqli_fetch_assoc($cartQ);
  $items=json_decode($result['Items'],true);
  $updated_items=array();

  $domain=(($_SERVER['HTTP_HOST']!='localhost')?'.'.$_SERVER['HTTP_HOST']:false);

  if($mode=='removeone'){
    foreach ($items as $item) {
      if ($item['id']==$edit_id && $item['size']==$edit_size) {
        $item['quantity']=$item['quantity']-1;
      }
      if ($item['quantity']>0) {
        $updated_items[]=$item;
      }
    }
  }

  if($mode=='addone'){
    foreach ($items as $item) {
      if ($item['id']==$edit_id && $item['size']==$edit_size) {
        $item['quantity']=$item['quantity']+1;
      }

        $updated_items[]=$item;

    }
  }

  if (!empty($updated_items)) {
    $json_update=json_encode($updated_items);
    $db->query("UPDATE Cart SET Items='{$json_update}' WHERE ID='{$cart_id}'");
    $_SESSION['success_flash']='Your shopping cart has been updated';

  }

  if (empty($updated_items)) {
    $db->query("DELETE FROM Cart WHERE id='{$cart_id}'");
    setcookie(CART_COOKIE,'',1,'/',null);
  }

 ?>
