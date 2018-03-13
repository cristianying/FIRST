<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
  $product_id=sanitize($_POST['product_id']);
  $size=sanitize($_POST['size']);
  $available=sanitize($_POST['available']);
  $quantity=sanitize($_POST['quantity']);
  $item=array();
  $item[]=array(
    'id'        =>$product_id,
    'size'      =>$size,
    'quantity'  =>$quantity,
  );

  $domain = ($_SERVER['HTTP_HOST'] != '.localhost')?'.'.$_SERVER['HTTP_HOST']:false;
  $query=$db->query("SELECT * FROM products WHERE ID='{$product_id}'");
  $product=mysqli_fetch_assoc($query);
  $_SESSION['success_flash']=$product['Title'].' was added to your cart.';

  //check to see if the cart cookie exists
  if ($cart_id!='') {

    $cartQ=$db->query("SELECT * FROM Cart WHERE ID='{$cart_id}'");
    $cart=mysqli_fetch_assoc($cartQ);
    //true means is gonna pass a associative array and not an object
    $previous_items=json_decode($cart['Items'],true);

    //$_SESSION['success_flash']=$previous_items['id'];

    $item_match =0;
    $new_items=array();
    //add quantity if same item and size is added again
    foreach ($previous_items as $pitem) {

      if ($item[0]['id'] == $pitem['id'] && $item[0]['size'] == $pitem['size']) {
        $pitem['quantity']=$pitem['quantity']+$item[0]['quantity'];
        if ($pitem['quantity']>$available) {
          $pitem['quantity']=$available;
        }
        $item_match=1;
        }
        //it keeps adding pitem in new_times everytime it loops
        //arrays can be added to a multidimensional array
        $new_items[]=$pitem;
    }

    //$new_items[]=$item[0];
    //if there is no match just add item to previous items.
    if ($item_match!=1) {
    $new_items=array_merge($item,$previous_items);
  }





    $items_json=json_encode($new_items);
    $cart_expire=date("Y-n-d H:i:s", strtotime("+30 days"));
    $db->query("UPDATE Cart SET Items='{$items_json}',Expire_date='{$cart_expire}' WHERE ID='{$cart_id}'");
    setcookie(CART_COOKIE,'',1,'/',null);
    setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, '/', null);

}
  else{
    //add the cart to the database and set COOKIE


    $items_json=json_encode($item);
    $cart_expire=date("Y-m-d H:i:s",strtotime("+30 days"));
    $db->query("INSERT INTO Cart (Items,Expire_date) VALUES ('{$items_json}','{$cart_expire}')");
    //last inserted id in the databse setting = to $cart_id
    $cart_id=$db->insert_id;
    //setcookie('CART_COOKIE',$cart_id,CART_COOKIE_EXPIRE,'/',$domain, false);

    setcookie(CART_COOKIE, $cart_id, CART_COOKIE_EXPIRE, '/', null);
    //setcookie(CART_COOKIE,$cart_id,CART_COOKIE_EXPIRE,'/',$domain,false);
  }
 ?>
