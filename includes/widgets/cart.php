<h3 class="text-center">Shopping Cart</h3>
<div>
<?php if(empty($cart_id)): ?>
 <p>Your Shopping Cart is empty.</p>
<?php else:
  $cartQ = $db->query("SELECT * FROM cart WHERE id={$cart_id}"); 
  $results = $cartQ->fetch_assoc();
  $items = json_decode($results['items'],true);
  $sub_total = 0;  
  ?>
  <table class="table table-condensed" id="cart_widget">
   <tbody>
    <?php foreach ($items as $item): 
      $productQ = $db->query("SELECT * FROM products WHERE id = '{$item['id']}'");
      $product = $productQ->fetch_assoc();
      ?>
      <tr>
        <td><?=$item['quantity'];?></td>
        <td><?=substr($product['title'],0,16);?></td>
        <td><?=money($item['quantity'] * $product['price']); ?> </td>
      </tr>
      
    <?php
      $sub_total += ($item['quantity'] * $product['price']);
     endforeach;?>
    <tr>
      <td></td>
      <td>Sub Total</td>
      <td><?=money($sub_total);?></td>
    </tr>
   </tbody>
  </table>
   <a href="cart.php" class="btn btn-xs btn-primary pull-right">View Cart</a>
   <div class="clearfix"></div>

<?php endif; ?>
</div>