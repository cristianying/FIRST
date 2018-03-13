<?php
//define constants
define('BASEURL',$_SERVER['DOCUMENT_ROOT'].'/tutorial/');
define('CART_COOKIE','SBwi72UCklwiqzz2');
define('CART_COOKIE_EXPIRE',time()+(86400*30));
define('TAXRATE',0.10);

define('CURRENCY','hkd');
define('CHECKOUTMODE','TEST');//change test to live when you are ready to go live

if (CHECKOUTMODE=='TEST') {
  define('STRIPE_PRIVATE','sk_test_5kYOnW6VE5jE7YR7nXU5UvrQ');
  define('STRIPE_PUBLIC','pk_test_7Ixp0Dt01ix2XlEydh2xEVRd');
}

if (CHECKOUTMODE=='LIVE') {
  define('STRIPE_PRIVATE','');
  define('STRIPE_PUBLIC','');
}
 ?>
