<?php
define('BASEURL', $_SERVER['DOCUMENT_ROOT'].'/tutorial/');
define('CART_COOKIE', 'AHwijGHGKgdjdg007');
define('CART_COOKIE_EXPIRE',time() + (86400 * 30));
define('TAXRATE', 0.087);  //Sales Video

define('CURRENCY','usd');
define('CHECKOUTMODE', 'TEST'); //Chang Test to live when u wanna go LIVE

if(CHECKOUTMODE =='TEST'){
  define('STRIPE_PRIVATE','sk_test_AGuEFEh1r7ycJkx6MiqBRvzG00l3af86Np');
  define('STRIPE_PUBLIC','pk_test_BblPwULY9PZuYGSjo1xd4fjw003t2rqGKU');
}

if(CHECKOUTMODE =='LIVE'){
  define('STRIPE_PRIVATE','');
  define('STRIPE_PUBLIC','');
}

