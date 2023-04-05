<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

$sql = "SELECT * FROM `products` WHERE deleted = 1";
$res = $db->query($sql);
$numR = $res->num_rows;

if(isset($_GET['unarchive'])){
  $unarchID = sanitize($_GET['unarchive']);
  $db->query("UPDATE products SET deleted = 0 WHERE id = $unarchID");

  header('Location: archived.php');
}

?>


<br>
<h2 class="text-center">Archived Products</h2>
<hr>
<table class="table arch-tab table-bordered table-striped table-condensed table-responsive">
<thead> 
<tr>
<th></th>
<th>Product</th>
<th>Price</th>
<th>Category</th>
<th>Sold</th>
</tr>
</thead>
  
<?php if($numR > 0){ ?>
<tbody>
<?php
while($product = $res->fetch_assoc()) : 
  $childID = $product['categories'];
  $cRes = $db->query("SELECT * FROM categories WHERE id = $childID");
  $child =$cRes->fetch_assoc();

  $parentID = $child['parent'];
  $pRes = $db->query("SELECT * FROM categories WHERE id = '$parentID'");
  $parent = $pRes->fetch_assoc();
  $child = $child['category'];
  $parent = $parent['category'];
  $category = $parent."-".$child;

?>
  <tr>
    <td><a href="archived.php?unarchive=<?=$product['id']; ?>" class="btn btn-info btn-xs"><i class="material-icons">unarchive</i></a></td>

    <td><?=$product['title']; ?></td>
    <td><?=money($product['price']); ?></td>
    <td><?= $category ;?></td>
    <td>0</td>
  </tr>
<?php endwhile; ?>
</tbody>
  <?php }?>
</table>

  <?php if($numR < 1){ ?>   
      <marquee class="text-danger" behavior="alternate" direction="left" scrollamount="13"><h3>Archived is empty!!!</h3></marquee> 
  <?php } ?>


<?php 
include 'includes/footer.php';
?>
