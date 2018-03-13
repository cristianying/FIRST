<?php
require_once 'core/init.php';
include 'includes/head.php';
include 'includes/navigations.php';
include 'includes/headerpartial.php';


if($cart_id!=''){


  $cartQ=$db->query("SELECT * FROM Cart WHERE ID='{$cart_id}'");
  $result=mysqli_fetch_assoc($cartQ);
  $items=json_decode($result['Items'],true);
  $i=1;
  $sub_total=0;
  $item_count=0;
  // var_dump($result);
  // array(4) {
  //   ["ID"]=> string(2) "25" ["Items"]=> string(157) "[
  //     {"id":"3","size":"medium","quantity":"1"},
  //     {"id":"2","size":"28","quantity":"1"},
  //     {"id":"1","size":"28","quantity":"3"},
  //     {"id":"1","size":"28","quantity":"1"}]"
  //     ["Expire_date"]=> string(19)
  //     "2018-04-06 04:27:09" ["Paid"]=> string(1) "0" }
  // var_dump($items);
  // array(4) {
  //   [0]=> array(3) { ["id"]=> string(1) "3" ["size"]=> string(6) "medium" ["quantity"]=> string(1) "1" }
  //   [1]=> array(3) { ["id"]=> string(1) "2" ["size"]=> string(2) "28" ["quantity"]=> string(1) "1" }
  //   [2]=> array(3) { ["id"]=> string(1) "1" ["size"]=> string(2) "28" ["quantity"]=> string(1) "3" }
  //   [3]=> array(3) { ["id"]=> string(1) "1" ["size"]=> string(2) "28" ["quantity"]=> string(1) "1" } }

}
 ?>


 <div class="col-sm-12">
     <h2 class="text-center">My Shopping Cart</h2><hr>
     <!--cart_id is gotten from the init.php-->
     <?php if($cart_id==''): ?>
     <div class="bg-danger">
       <p class="text-center">
         Your shopping cart is empty
       </p>
     </div>
   <?php else: ?>
     <table class="table table-bordered table-condensed table-striped">
       <thead>
         <th>#</th>
         <th>Item</th>
         <th>Price</th>
         <th>Quantity</th>
         <th>Size</th>
         <th>Sub Total</th>
       </thead>
       <tbody>

         <?php

          foreach ($items as $item) {
            $product_id=$item['id'];
            $productQ=$db->query("SELECT *FROM products WHERE ID='{$product_id}'");
            $product=mysqli_fetch_assoc($productQ);
            $sArray=explode(',',$product['Sizes']);
            foreach($sArray as $sizeString){
              $s=explode(':',$sizeString);
              if($s[0]==$item['size']){
                $available=$s[1];
              }
            }
            ?>
            <tr>
              <td><?=$i;?></td>
              <td><?=$product['Title'];?></td>
              <td><?=money($product['Price']);?></td>

              <td>
              <button class="btn btn-xs btn-dafault" onclick="update_cart('removeone','<?=$product['ID'];?>','<?=$item['size'];?>');" >-</button>
              <?=$item['quantity'];?>
              <?php if($item['quantity']<$available): ?>
              <button class="btn btn-xs btn-dafault" onclick="update_cart('addone','<?=$product['ID'];?>','<?=$item['size'];?>');" >+</button>
              <?php else: ?>
              <span class="text-danger">No stock</span>
              <?php endif; ?>
              </td>

              <td><?=$item['size'];?></td>
              <td><?=money($item['quantity']*$product['Price']);?></td>
            </tr>
            <?php
          $i++;
          $item_count+=$item['quantity'];
          $sub_total+=($product['Price']*$item['quantity']);
            }
            $tax=TAXRATE*$sub_total;
            //format to 2 decimal places
            $tax=number_format($tax,2);
            $grand_total=$tax+$sub_total;


            ?>

       </tbody>
     </table>
     <table class="table table-bordered table-condensed text-right">
       <legend>Totals</legend>
       <thead class="totals-table-header">
         <th>Total Items</th>
         <th>Sub Total</th>
         <th>Tax</th>
         <th>Grand Total</th>
       </thead>
       <tbody>
         <tr>
           <td><?=$item_count;?></td>
           <td><?=money($sub_total);?></td>
           <td><?=money($tax);?></td>
           <td class="bg-success"><?=money($grand_total);?></td>
         </tr>
       </tbody>
     </table>
     <!-- check out button -->




<button type="button" class="btn btn-primary float-right"  data-toggle="modal" data-target="#checkoutModal">
<img src="iconic/svg/cart.svg" style="width:20px; filter:invert();"> &nbsp; Check out >>
</button>



<!-- Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">

        <h5 class="modal-title" id="checkoutModalLabel">Shipping Address</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="thankYoustripe.php" method="post" id="payment-form">

            <span class="" id="payment-errors"></span>
            <input type="hidden" name="tax" value="<?=$tax;?>">
            <input type="hidden" name="sub_total" value="<?=$sub_total;?>">
            <input type="hidden" name="grand_total" value="<?=$grand_total;?>">
            <input type="hidden" name="cart_id" value="<?=$cart_id;?>">
            <input type="hidden" name="description" value="<?=$item_count.' item'.(($item_count>1)?'s':'').' from ying boutique.';?>">


            <div class="container">
              <div id="step1">
              <div class="form-row">



                <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="first_name" placeholder="First Name">
                  <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="last_name" placeholder="Last Name">
                    <input type="email" class="form-control mb-3 StripeElement StripeElement--empty" name="email" placeholder="Email">
                    <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="street"  placeholder="address_line1">
                    <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="street2" placeholder="address_line2">
                    <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="city" placeholder="address_city">
                    <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="state" placeholder="address_state">
                    <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="zip_code" placeholder="address_zip">
                    <input type="text" class="form-control mb-3 StripeElement StripeElement--empty" name="country"  placeholder="address_country">
                    </div>
                    </div>
                    <div id="step2">
                    <div class="form-row">
                <!-- <label for="card-element">
                  Credit or debit card
                </label> -->

                <div id="card-element" class="form-control">
                  <!-- A Stripe Element will be inserted here. -->
                </div>

                <!-- Used to display form errors. -->
                <div id="card-errors" role="alert"></div>
                <hr>

                <button type="submit" class="btn btn-primary float-right" onclick="" id="check_out_button" style="display:none;">Check Out >></button>
              </div>

                </div>
              </div>



          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
<button type="button" class="btn btn-primary float-left" onclick="back_address();" id="back_button"style="display:none;"><< Back</button>
          <button type="button" class="btn btn-primary" onclick="check_address();" id="next_button">Next >></button>

          <!-- <button>Submit Payment</button> -->
            </div>
          </form>
      </div>
    </div>
  </div>
</div>


   <?php endif; ?>
 </div>

 <script>
  function back_address(){
    jQuery('#payment-errors').html("");
    jQuery('#step1').css("display","block");
    jQuery('#step2').css("display","none");
    jQuery('#next_button').css("display","inline-block");
    jQuery('#back_button').css("display","none");
    jQuery('#check_out_button').css("display","none");
    jQuery('#checkoutModalLabel').html("Shipping Address");
  }

   //dont want to have this functio on every page
   function check_address(){

            jQuery('#payment-errors').html("");
            jQuery('#step1').css("display","none");
            jQuery('#step2').css("display","block");
            jQuery('#next_button').css("display","none");
            jQuery('#back_button').css("display","inline-block");
            jQuery('#check_out_button').css("display","inline-block");
            jQuery('#checkoutModalLabel').html("Enter your card details");
        }





    </script>

 <?php
include 'includes/footer.php';
  ?>
