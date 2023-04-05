<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

// Delete Product
if(isset($_GET['delete'])){
 $id = sanitize($_GET['delete']);
 $query = "UPDATE products SET deleted = 1 WHERE id ='$id';"; //SET deleted=1,featured=0 where...
 $query .= "UPDATE products SET featured = 0 WHERE id ='$id'";

 if($db->multi_query($query)){
 header('Location: products.php');
 }
}


if (isset($_GET['add']) || isset($_GET['edit'])) {
  
$dbPath='';   
$brandQuery = $db->query("SELECT * FROM brand ORDER BY brand");
$parentQuery = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
$title = ((isset($_POST['title']) && $_POST['title'] !='')?sanitize($_POST['title']):'');
$brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?sanitize($_POST['brand']):'');
$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']):'');
$category = ((isset($_POST['child']) && !empty($_POST['child']))?sanitize($_POST['child']):'');
$price = ((isset($_POST['price']) && $_POST['price'] !='')?sanitize($_POST['price']):'');
$list_price = ((isset($_POST['list_price']) && $_POST['list_price'] !='')?sanitize($_POST['list_price']):'');
$description = ((isset($_POST['description']) && $_POST['description'] !='')?sanitize($_POST['description']):'');
$sizes = ((isset($_POST['sizes']) && $_POST['sizes'] !='')?sanitize($_POST['sizes']):'');
$sizes = rtrim($sizes,',');
$saved_photo='';



if (isset($_GET['edit'])) {
  $edit_id = (int)$_GET['edit'];
  $productRes = $db->query("SELECT * FROM products WHERE id = '$edit_id'"); 
  $product = $productRes->fetch_assoc();
  if (isset($_GET['delete_photo'])) {
    $imgI = (int)$_GET['imgI'] - 1;
    $images = explode(',',$product['image']);
    $image_url =$_SERVER['DOCUMENT_ROOT'].$images[$imgI];
    unlink($image_url);
    unset($images[$imgI]);
    $imgString = implode(',',$images);
    $db->query("UPDATE products SET image ='{$imgString}' WHERE id ='$edit_id'");
    header('Location: products.php?edit='.$edit_id);
  }
  $category = ((isset($_POST['child']) && $_POST['child'] !='')?sanitize($_POST['child']):$product['categories']);
  $title = ((isset($_POST['title']) && $_POST['title'] !='')?sanitize($_POST['title']):$product['title']);
  $brand = ((isset($_POST['brand']) && $_POST['brand'] !='')?sanitize($_POST['brand']):$product['brand']);
  $parentQ = $db->query("SELECT * FROM categories WHERE id ='$category'");
  $parentR = $parentQ->fetch_assoc();
  $parent = ((isset($_POST['parent']) && $_POST['parent'] !='')?sanitize($_POST['parent']):$parentR['parent']);
  $price = ((isset($_POST['price']) && $_POST['price'] !='')?sanitize($_POST['price']):$product['price']);
  $list_price = ((isset($_POST['list_price']))?sanitize($_POST['list_price']):$product['list_price']);
  $description = ((isset($_POST['description']))?sanitize($_POST['description']):$product['description']);
  $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] !='')?sanitize($_POST['sizes']):$product['sizes']);
  $sizes = rtrim($sizes,',');
  $saved_photo=(($product['image'] !='')?$product['image']:'');
  $dbPath = $saved_photo;

}
if (!empty( $sizes)) {
  $sizeString = sanitize($sizes);
  $sizeString = rtrim($sizeString,',');
  $sizesArray = explode(',',$sizeString);
  $sArray = array();
  $qArray = array();
  $tArray = array();
  foreach ($sizesArray as $ss) {
    $s = explode(':', $ss);
    $sArray[] = $s[0];
    $qArray[] = $s[1];
    $tArray[] =  (!empty($s[2]))?$s[2]:0;
  
    }
}else {$sizesArray = array();}

if ($_POST) {
  
  $errors = [];
 
$required = array('title', 'brand', 'price', 'child','parent', 'sizes');
$allowed = array('jpg','png','jpeg','gif','webp');
$uploadPath = [];
$tmpLoc = [];
foreach ($required as $field) {
   if ($_POST[$field] == '') {
     $errors[] .= 'All Fields With an Asterik are required';
     break;
   }
}
//var_dump($_FILES['photo']);
$photoCount = count($_FILES['photo']['name']);
 if ($photoCount > 0 && !empty($_FILES['photo']['name'][0])) { 
   for($i=0; $i<$photoCount; ++$i){ //echo $photoCount; 
     $name = $_FILES['photo']['name'][$i];
   $nameArray = explode('.',$name);
   $fileName = $nameArray[0];
   $fileExt = $nameArray[1]; 
    $mime = explode('/',$_FILES['photo']['type'][$i]);
    // $mimeType = $mime[0];
    // $mimeExt = $mime[1];
    // $mimeType = strtolower($mimeType);

     $fileExt = strtolower($fileExt);
     //echo $fileExt;
     $tmpLoc[] = $_FILES['photo']['tmp_name'][$i];

     $fileSize = $_FILES['photo']['size'][$i];
     $uploadName = md5(microtime().$i).'.'.$fileExt;
     $uploadPath[] = BASEURL.'images/products/upload/'.$uploadName;
     if ($i != 0) {
       $dbPath .=',';
     }
     $dbPath .= '/tutorial/images/products/upload/'.$uploadName;

    
    if (!in_array($fileExt, $allowed)) {
      $errors[] = 'The file extension must be a png, jpg, jpeg or gif.';
    } 
    if($fileSize > 8048576){
       $errors[] = "The File Size must not be bigger than 8mb.";
    }
  }
 }
if (!empty($errors)) {
  echo display_errors($errors);
}else {
  
    if($photoCount > 0){
  // upload file and insert into db
  for($i=0; $i<$photoCount; $i++){
  move_uploaded_file($tmpLoc[$i],$uploadPath[$i]);
  }
}
$insertSql  = "INSERT INTO products (`title`,`price`,`list_price`,`brand`,`categories`,`image`,`description`,`sizes`)
 VALUES('$title','$price','$list_price','$brand','$category','$dbPath ','$description','$sizes')";
 if (isset($_GET['edit'])) {
  $insertSql ="UPDATE products SET title ='$title', price ='$price', list_price='$list_price', brand='$brand', categories='$category', sizes ='$sizes', image='$dbPath', description='$description' WHERE id ='$edit_id'"; 
 }

 $db->query($insertSql);
 header('Location: products.php');
}

}

?>
<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add A New'); ?> Product</h2><hr>
<form action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1'); ?>" method="POST" enctype="multipart/form-data">
  <div class="form-group col-md-3">
    <label for="title">Title<span class="text-danger">*</span>:</label>
    <input type="text" name="title" class="form-control" id="title" value="<?=$title;?>">
  </div>
  <div class="form-group col-md-3">
   <label for="brand">Brand<span class="text-danger">*</span>:</label>
   <select name="brand" class="form-control" id="brand">
    <option value="" <?=(($brand == '')?' selected':'') ;?> ></option>
    <?php while($b = $brandQuery->fetch_assoc()): ?>
     <option value="<?=$b['id']; ?>"<?=(($brand == $b['id'])?' selected':''); ?>><?=$b['brand']; ?></option>

<?php endwhile; ?>
   </select>
  </div>
  <div class="form-group col-md-3">
  <label for="parent">Parent Category<span class="text-danger">*</span>:</label>
  <select name="parent" class="form-control" id="parent">
   <option value=""<?=(($parent == '')?'selected':''); ?>></option>
   <?php while($p = $parentQuery->fetch_assoc()) : ?>
      <option value="<?=$p['id']; ?>" <?=(($parent == $p['id'])?' selected':'');?>><?=$p['category']; ?></option>

<?php endwhile; ?>
  </select>
   
  </div>
  <div class="form-group col-md-3">
    <label for="child">Child Category<span class="text-danger">*</span>:</label>
    <select name="child" id="child" class="form-control"></select>
  </div>
  <div class="form-group col-md-3">
   <label for="price">Price<span class="text-danger">*</span>:</label>
   <input type="text" name="price" class="form-control" id="price" value="<?= $price;?>">
  </div>

  <div class="form-group col-md-3">
   <label for="list_price">List Price:</label>
   <input type="text" name="list_price" class="form-control" id="list_price" value="<?=$list_price; ?>">
  </div>
  <div class="form-group col-md-3">
  <label for="">Quanity & Sizes<span class="text-danger">*</span>:</label>
   <button class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
  </div>
  <div class="form-group col-md-3">
   <label for="sizes">Sizes & Qty Preview</label>
   <input type="text" name="sizes" class="form-control" id="sizes" value="<?=$sizes; ?>" readonly>
  </div>
  <div class="form-group col-md-6">
  <?php if($saved_photo !=''): ?>
  <?php 
  $imgI = 1;
  $images = explode(',',$saved_photo); ?>
  <?php foreach($images as $image) : ?>
  <div class="saved-photo col-md-4"><img src="<?=$image;?>" alt="Saved Photo"><br>
  <a href="products.php?delete_photo=1&edit=<?=$edit_id;?>&imgI=<?=$imgI;?>" class="text-danger">Delete Photo</a>
  </div>
  <?php 
   $imgI++;
  endforeach; ?>
<?php else: ?>
   <label for="photo">Product Photo</label>
   <input type="file" name="photo[]" id="photo" class="form-control" multiple>
  <?php endif; ?>       
  </div>
  <div class="form-group col-md-6">
   <label for="description">Description:</label>
   <textarea name="description" class="form-control" id="description" cols="30" rows="6"><?=$description; ?></textarea>
  </div>
  <div class="form-group pull-right">
  <a href="products.php" class="btn btn-default">Cancel</a>
    <input type="submit" value="<?=((isset($_GET['edit']))?'Edit':'Add'); ?> Product" class="btn btn-success pull-right">
  </div>
  <div class="clearfix"></div>
</form>



<!-- Modal -->
<div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="sizesModalLabel">Size & Quantity</h5>
      </div>
      <div class="modal-body">
      <div class="container-fluid">
         <?php for ($i=1; $i <= 12; $i++): ?> 
          <div class="form-group col-md-2">
           <label for="size<?=$i;?>">Size:</label>
           <input type="text" name="size<?=$i; ?>" id="size<?=$i; ?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:''); ?>" class="form-control">
          </div>
          <div class="form-group col-md-2">
           <label for="qty<?=$i;?>">Quantity:</label>
           <input type="number" name="qty<?=$i; ?>" id="qty<?=$i; ?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:''); ?>" min="0" class="form-control">
          </div>
          <div class="form-group col-md-2">
           <label for="threshold<?=$i;?>">Threshold:</label>
           <input type="number" name="threshold<?=$i; ?>" id="threshold<?=$i; ?>" value="<?=((!empty($tArray[$i-1]))?$tArray[$i-1]:''); ?>" min="0" class="form-control">
          </div>
          <?php endfor; ?>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
      </div>
    </div>
  </div>
</div>

  
<?php }else{

$sql = "SELECT * FROM products WHERE deleted = 0 ORDER BY NOT featured";
$presults = $db->query($sql);
if (isset($_GET['featured'])) {
   $id = (int)$_GET['id'];
   $featured = (int)$_GET['featured'];
   $featuredSql = "UPDATE products SET featured ='$featured' WHERE id ='$id'";
   $db->query($featuredSql);
   header('Location: products.php');
}
?>

<br>
<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a><div class="clearfix"></div>
<hr>
<table class="table table-bordered table-responsive table-condensed table-striped">
<thead>
<th></th>
<th>Product</th>
<th>Price</th>
<th>Category</th>
<th>Featured</th>
<th>Sold</th>
</thead>


<tbody>
 <?php while($product = $presults->fetch_assoc()): 
  $childID = $product['categories'];
  $catSql = "SELECT * FROM categories WHERE id = '$childID'";
  $result = $db->query($catSql);
  $child = $result->fetch_assoc();
  $parentID = $child['parent'];
  $pSql = "SELECT * FROM categories WHERE id = '$parentID'";
  $presult = $db->query($pSql);
  $parent = $presult->fetch_assoc();
  $category = $parent['category'].'-'.$child['category'];
  ?>
 <tr>
   <td>
   <a href="products.php?edit=<?=$product['id']; ?>" data-toggle="tooltip" title="Edit" data-placement="right" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></a>
   <a href="products.php?delete=<?=$product['id']; ?>" data-toggle="tooltip" title="Archive" data-placement="right" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove-sign"></span></a>

   </td>
   <td><?=$product['title']; ?></td>
   <td><?=money($product['price']); ?></td>
   <td><?=$category; ?></td>
   <td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id']; ?>" class="btn btn-xs btn-default">
   <span class=" glyphicon glyphicon-<?=(($product['featured'] ==1)?'minus':'plus'); ?>"></span>
   </a>&nbsp <?=(($product['featured'] == 1)?'Featured Product':''); ?></td>
   <td>0</td>
 
 </tr>


<?php endwhile; ?>
</tbody>
</table>

<?php
}
include 'includes/footer.php';?>
<script>
 jQuery('document').ready(function(){
get_child_options('<?=$category;?>');
 });

$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});

</script>