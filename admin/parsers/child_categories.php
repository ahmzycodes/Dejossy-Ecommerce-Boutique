<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
$parentID = (int)$_POST['parentID'];
$selected = sanitize($_POST['selected']);
$childQuery = $db->query("SELECT * FROM categories WHERE parent = '$parentID' ORDER BY category");
ob_start();
?>

<option value=""></option>
<?php while($child = $childQuery->fetch_assoc()): ?>
 <option value="<?=$child['id']; ?>"<?=(($selected == $child['id'])?' selected':'');?>><?=$child['category']; ?></option>

<?php endwhile; ?>


<?php echo ob_get_clean();?>

