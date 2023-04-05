<!-- Top Navbar-->
<?php
$sql = "SELECT * FROM categories WHERE parent = 0";
$pquery = $db->query($sql);
?> 	

<nav class="navbar navbar-default navbar-fixed-top">
   	 <div class="container">
		<div class="brand">
    <a href="index.php"><img src="images/headerlogo/logaster/logo.png" id="logo" alt="Logo Pic"></a>      
		<a href="index.php" class="logoname navbar-brand"><span id="brandname">e Jossy's Boutique</span></a>
		</div>
		<ul class="nav navbar-nav">
		 <?php 
			while($parent = $pquery->fetch_assoc()) : ?>
			<?php 
			$parent_id = $parent['id'];
			$sql2 = "SELECT * FROM categories WHERE parent  = '$parent_id'";
			$cquery = $db->query($sql2);
			
			?>
			
             <!--Menu Items -->
			<li class="dropdown drop-parents">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<? echo $parent['category']; ?><span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
				<?php while($child = $cquery->fetch_assoc()) : ?>
            <li><a href="category.php?cat=<?=$child['id']; ?>"><?=$child['category']?></a></li>
                <? endwhile;?>
				</ul>
			</li>
    <? endwhile;?>
		<li><a href="cart.php"> <!--<span class="my-cart glyphicon glyphicon-shopping-cart"></span>--><i id="my-cart" class="material-icons">shopping_cart</i></a></li>
		</ul>
	</div>
</nav>