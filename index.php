 	<?php 
	 require_once 'core/init.php';
      include 'includes/head.php';
      include 'includes/navigation.php';
      include 'includes/headerfull.php';
	  include 'includes/leftbar.php';

	  $sql = "SELECT * FROM products WHERE featured = 1";
	  $featured = $db->query($sql);
     
	  ?>
   <!--Main Content-->
 <div class="col-md-8">
 	<div class="row">
 		<h2 class="text-center">
 			Featured Products
 		</h2>
        <?php while($product = $featured->fetch_assoc()) : ?>
			<div class="col-md-3">
				<h4 class="levis"><b><?= $product['title']; ?></b></h4>
				<?php $photos = explode(',',$product['image']); ?>
				<img src="<?= $photos[0]; ?>" alt="<?= $product['title']; ?>" class="image_thumb" />
				<p class="list-price text-danger">List Price: <s><?= money($product['list_price']); ?></s></p>

				<!-- NB: The <s></s> helps srikes through text-->
				<p class="price">Our Price: <span class="bg-success"><?= money($product['price']); ?></span></p>
				<p class="list-p text-danger">You Save <?=saveP($product['list_price'],$product['price']);?>% ($<?=$product['list_price'] - $product['price'];?>)</p>

				<button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?=$product['id']; ?>)">Details</button>
			</div> 

 	<?php endwhile; ?> 
 	</div>
 </div>

<?php
      include 'includes/rightbar.php';
      include 'includes/footer.php';

?>




  
