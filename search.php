<?php 
	 require_once 'core/init.php';
      include 'includes/head.php';
      include 'includes/navigation.php';
      include 'includes/headerpartial.php';
	  include 'includes/leftbar.php';
    
    $sql = "SELECT * FROM products";
    $cat_id = ((isset($_POST['cat']) && $_POST['cat'] != '')?sanitize($_POST['cat']):'');
    if($cat_id ==''){
      $sql .= " WHERE deleted = 0";
    }else{
      $sql .=" WHERE categories = '{$cat_id}' AND deleted = 0";
    }
    $price_sort = ((isset($_POST['price_sort']) && $_POST['price_sort'] !='')?sanitize($_POST['price_sort']):'');
    $min_price = (isset($_POST['min_price']) && ($_POST['min_price'] !='')?sanitize($_POST['min_price']):'');
    $max_price = ((isset($_POST['max_price']) && $_POST['max_price'] !='')?sanitize($_POST['max_price']):'');
    $brand     = ((isset($_POST['brand']) && $_POST['brand'] !='')?sanitize($_POST['brand']):'');
    
    if ($min_price !='') {
        $sql .= " AND price >= '{$min_price}'";
    }
    if ($max_price !='') {
      $sql .= " AND price <= '{$max_price}'";
  }
    if($brand !=''){
      $sql .= " AND brand = '{$brand}'";
    }
    if($price_sort =='low'){
      $sql .=" ORDER BY price";
    }
    if($price_sort =='high'){
      $sql .=" ORDER BY price DESC";
    }
    $productQ = $db->query($sql);
    $category = get_cat($cat_id);
     
	  ?>
   <!--Main Content-->
 <div class="col-md-8">
 	<div class="row">
 		<h2 class="text-center">
       <?php if($cat_id != ''):  ?>
 			<?=$category['parent'].' '.$category['child']; ?>
     </h2>
  <?php else: ?>
    <h2 class="text-center">Dejossy's Boutique</h2>


  <?php endif; ?>
        <?php while($product = $productQ->fetch_assoc()) : ?>
			<div class="col-md-3">
        <h4 id="levis"><?= $product['title']; ?></h4>
        <?php $photos = explode(',', $product['image']); ?>
				<img src="<?= $photos[0]; ?>" alt="<?= $product['title']; ?>" class="image_thumb" />
				<p class="list-price text-danger">List Price: <s><?= $product['list_price']; ?></s></p>
				<!-- NB: The <s></s> helps srikes through text-->
        <p class="price">Our Price: <?= $product['price']; ?></p>
				<p class="list-p text-danger">You Save <?=saveP($product['list_price'],$product['price']);?>% ($<?=$product['list_price'] - $product['price'];?>)</p>        
				<button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?= $product['id']; ?>)">Details</button>
			</div> 

 	<?php endwhile; ?>
 	</div>
 </div>

<?php
      include 'includes/rightbar.php';
      include 'includes/footer.php';

?>




  
