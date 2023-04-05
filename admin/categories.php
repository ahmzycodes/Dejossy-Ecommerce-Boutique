<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
    if (!is_logged_in()) {
        login_error_redirect();
    }
    include 'includes/head.php';
    include 'includes/navigation.php'; 

    $sql = "SELECT * FROM categories WHERE parent = 0";
    $result = $db->query($sql);
    $errors = [];
    $category = '';
    $post_parent = '';

//    Edit Category
if(isset($_GET['edit']) && !empty($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $edit_id = sanitize($edit_id);
    $edit_sql = "SELECT * FROM categories WHERE id = '$edit_id'";
    $edit_result = $db->query($edit_sql);
    $edit_category = $edit_result->fetch_assoc();
    
} 



    // Delete Category
    if (isset($_GET['delete']) && !empty($_GET['delete'])) {
        $delete_id = (int)$_GET['delete'];
        $delete_id = sanitize($delete_id);
        $sql = "SELECT * FROM categories WHERE id ='$delete_id'";
        $result = $db->query($sql);
        $category = $result->fetch_assoc();
        if ($category['parent'] == 0) {
              $sql = "DELETE FROM categories WHERE parent ='$delete_id'";
              $db->query($sql);
        }
        $del_sql = "DELETE FROM categories WHERE id ='$delete_id'";
        $db->query($del_sql);
        header("Location: categories.php");

    }
     
//    Process Form
 if (isset($_POST) && !empty($_POST)) {
     $post_parent = sanitize($_POST['parent']);
     $category = sanitize($_POST['category']);
     $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
     if (isset($_GET['edit'])) {
         $id = $edit_category['id'];
         $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent' AND id !='$id'";
     }
     
     $fresult = $db->query($sqlform);
     $count = $fresult->num_rows;
    //  if category is blank
    if($category ==""){
        $errors[] .= 'The category cannot be left blank';
    }

   // if exists
    if ($count > 0) {
        $errors[ ] .= $category.' already exists. Plesae choose a new category.';
    }

    // Display Errs || update database
    if (!empty($errors)) {
        // display
     $display = display_errors($errors); ?>
      
        
        <script>
    jQuery('document').ready(function(){
        jQuery('#errors').html('<?=$display; ?>');
    });
    </script>

   <?php } else {
    //    insert into db
    $usql = "INSERT INTO categories (category, parent) VALUES ('$category','$post_parent')";
    if(isset($_GET['edit'])) {
        $usql = "UPDATE categories SET category ='$category', parent= '$post_parent' WHERE id = '$edit_id'";
    }
    $db->query($usql);
    header('Location: categories.php');
    }
}

$category_value = '';
$parent_value = 0;
 if (isset($_GET['edit'])) {
     $category_value = $edit_category['category'];
     $parent_value = $edit_category['parent'];
 }elseif(isset($_POST)) {
     $category_value = $category;
     $parent_value = $post_parent;
 }


?>


<h2 class="text-center">Categories</h2><hr>
<div class="row">

<!-- Form -->
 <div class="col-md-6">
  <form action="categories.php<?=((isset($_GET['edit']))?'?edit='.$edit_id: ""); ?> " method="POST" class="form">
  <legend><?=((isset($_GET['edit']))?'Edit':'Add A'); ?> Category</legend>
  <div id="errors"></div>
   <div class="form-group">
    <label for="parent">Parent</label>
    <select name="parent" id="parent" class="form-control">
    <option value="0"><?(($parent_value ==0)?'selected="selected"':'');?>Parent</option>
    <?php while($parent = $result->fetch_assoc()): ?>
    <option value="<?=$parent['id']; ?>"<?=(($parent_value == $parent['id'])?'selected="selected"':''); ?>><?=$parent['category']; ?></option>
<?php endwhile; ?>
    </select>
   </div>
    <div class="form-group">
     <label for="category">Category</label>
     <input type="text" class="form-control" id="category" name="category" value="<?=$category_value; ?>">
    </div>
    <div class="form-gorup">
    <?php if(isset($_GET['edit'])) {?>
     <a href="categories.php" role="button" class="btn btn-default can">Cancel</a>
     <?php } ?>
     <input type="submit" value="<?=((isset($_GET['edit']))?'Edit':'Add'); ?> Category" class="btn btn-success">
    </div>
  </form>
 </div>



 <!-- Category Table -->
 <div class="col-md-6">
    <table class="table cat_tbl">
    <thead>
     <th>Category</th>
     <th>Parent</th>
     <th></th>
    </thead>
    <tbody>
    <?php
    
    $sql = "SELECT * FROM categories WHERE parent = 0";
    $result = $db->query($sql);
    while($parent = $result->fetch_assoc()): 
        $parent_id = (int)$parent['id'];
        $sql2 = "SELECT * FROM categories WHERE parent ='$parent_id'";
        $cresult = $db->query($sql2);
    ?>
     <tr class="bg-primary">
     <td><strong><?=$parent['category'];?></strong></td>
     <td>Parent</td>
     <td><a href="categories.php?edit=<?=$parent['id']; ?>" class='btn btn-xs btn-default'><span class="glyphicon glyphicon-pencil"></span></a>
     <a href="categories.php?delete=<?=$parent['id']; ?>" class='btn btn-xs btn-default'><span class="glyphicon glyphicon-remove-sign"></span></a>
     </td>
     </tr>
 <?php while ($child = $cresult->fetch_assoc()){ ?>
  <tr class="bg-info">
  <td><?=$child['category'];?></td>
  <td><?=$parent['category']; ?></td>
  <td><a href="categories.php?edit=<?=$child['id']; ?>" class='btn btn-xs btn-default'><span class="glyphicon glyphicon-pencil"></span></a>
  <a href="categories.php?delete=<?=$child['id']; ?>" class='btn btn-xs btn-default'><span class="glyphicon glyphicon-remove-sign"></span></a>
  </td>
  </tr>
  <?php
   }
 ?>

<?php endwhile; ?>
    </tbody>
    </table>
 </div>

</div>

<?php
include 'includes/footer.php';
?>