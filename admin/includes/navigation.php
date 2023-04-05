<nav class="navbar navbar-default navbar-fixed-top">
   	 <div class="container">
		<div class="brand">
     <img  src="../images/headerlogo/logaster/logo.png" id="logo" alt="Logo  Pic">      
		<a  class="navbar-brand logoname" href="/tutorial/admin/index.php" ><span id="brandname">e Jossy's Boutique Admin</span></a>
		</div>

		<ul class="nav navbar-nav nav">
			
						 <!--Menu Items -->
				<li><a href="index.php">Dashboard</a></li>						 
       <li><a href="brands.php">Brands</a></li>
			 <li><a href="categories.php">Categories</a></li>
			 <li><a href="products.php">Products</a></li>
			 <li><a href="archived.php">Archived</a></li>
		    <?php if(has_permission('admin')): ?>
			 <li><a href="users.php">Users</a></li>
			<?php endif;  ?>
			<li class="dropdown">
			 <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hello <?=$user_data['first']; ?>!
			  <span class="caret"></span>
			 </a>
			 <ul class="dropdown-menu" role="menu">
			  <li><a href="change_password.php">Change Password</a></li>
			  <li><a href="logout.php">Log Out</a></li>
			 </ul>
			</li>


			


			<!-- <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<? echo $parent['category']; ?><span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
            <li><a href="#"></a></li>
				</ul>
			</li> -->
		</ul>
	</div>
</nav>