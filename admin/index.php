<?php
require_once '../core/init.php';
if (!is_logged_in()) {
    header('Location: login.php');
}
include 'includes/head.php';
include 'includes/navigation.php';
?>
<!-- Orders To Fill -->
<?php
$txnQuery = "SELECT t.id, cart_id, t.full_name, t.description, t.txn_date, t.grand_total, c.items, c.paid, c.shipped 
FROM transactions t 
LEFT JOIN cart c on t.cart_id =c.id 
WHERE c.paid = 1 AND c.shipped = 0 ORDER BY t.txn_date";
$txnResults = $db->query($txnQuery);

?>
<div class="col-md-12">
<h3 class="text-center">Orders To Ship</h3>
<table class="table table-condensed table-bordered table-striped">
 <thead>
    <th></th>
    <th>Name</th>
    <th>Description</th>
    <th>Total</th>
    <th>Date</th>
 </thead>
 <tbody> 
  <?php while($order = $txnResults->fetch_assoc()) : ?>
   <tr>
    <td>
        <a href="orders.php?txn_id=<?=$order['id']; ?>" class="btn btn-xs btn-info">Details</a>
    </td>
    <td><?=$order['full_name'];?></td>
    <td><?=$order['description'];?></td>
    <td><?=money($order['grand_total']); ?></td>
    <td><?=pretty_date($order['txn_date']); ?></td>
   </tr>
<?php endwhile; ?>  
 </tbody>
</table>
</div>

<div class="row">
    <!--Sales By MOnth -->
    <?php  
        $thisYr = date("Y");
        $lastYr = $thisYr -1;
    $thisYrQ = $db->query("SELECT grand_total, txn_date FROM transactions WHERE YEAR(txn_date) = '{$thisYr}'");
    $lastYrQ = $db->query("SELECT grand_total, txn_date FROM transactions WHERE YEAR(txn_date) = '{$lastYr}'");
    $current = [];
    $last = [];
    $currentTotal = 0;
    $lastTotal = 0;
    while ($x = $thisYrQ->fetch_assoc()) {
        //print_r($x);
        $month = date("m",strtotime($x['txn_date']));
        $month = (int)$month;
        if(!array_key_exists($month,$current)){
           // echo 'done<br>';
            $current[$month] = $x['grand_total'];
        }else{
            $current[$month] += $x['grand_total'];
        }
        $currentTotal += $x['grand_total'];
    }
    while ($y = $lastYrQ->fetch_assoc()) {
        $month = date("m",strtotime($y['txn_date']));
        $month = (int)$month;
        if(!array_key_exists($month,$last)){
            $last[$month] = $y['grand_total'];
        }else{
            $last[$month] += $y['grand_total'];
        }
        $lastTotal += $y['grand_total'];
    }



    ?>
  <div class="col-md-4">
    <h3 class="text-center">Sale By Month</h3>
    <table class="table table-condensed table-striped table-bordered">
        <thead>
            <th></th>
            <th><?=$lastYr;?></th>
            <th><?=$thisYr;?></th>
        </thead>
        <tbody>
        <?php  for($i=1; $i <=12; ++$i): 
            $dT = DateTime::createFromFormat('!m',$i);  
        ?>
            <tr <?=(date("m") == $i)?' class="text-info"':'';?>>
            <td><?=$dT->format("F");?></td>
            <td><?=(array_key_exists($i,$last))?money($last[$i]):money(0);?></td>
            <td><?=(array_key_exists($i,$current))?money($current[$i]):money(0);?></td>
            </tr>
        <? endfor; ?> 
        <tr>
            <td>Total</td>
            <td><?=money($lastTotal);?></td>
            <td><?=money($currentTotal);?></td>
        </tr>
        </tbody>
    </table>
    </div>

    <!-- inventory -->
    <?php
        $iQuery = $db->query("SELECT * FROM products WHERE deleted = 0");
        $lowItems = array();
            while($product = $iQuery->fetch_assoc()){
                $item = [];
                $sizes = sizesToArray($product['sizes']);
                foreach ($sizes as $size) {
                  if($size['quantity'] <= $size['threshold']) :
                    $cat = get_cat($product['categories']);
                    $item = array(
                        'title' => $product['title'],
                        'size' => $size['size'],
                        'quantity' => $size['quantity'],
                        'threshold' => $size['threshold'],
                        'category' =>$cat['parent'].' ~ '.$cat['child']
                    );
                    $lowItems[] = $item;
                endif;
                }
            }
    ?>
    <div class="col-md-8">
      <h3 class="text-center">Low Inventory</h3>
    <table class="table table-condensed table-striped table-bordered">
    <thead>
        <th>Product</th>
        <th>Category</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Threshold</th>
    </thead>
    <tbody>
    <?php foreach($lowItems as $item) : ?>
        <tr <?=($item['quantity'] == 0)?' class="danger"':'';?>>
          <td><?=$item['title'];?></td>
          <td><?=$item['category'];?></td>
          <td><?=$item['size'];?></td>
          <td><?=$item['quantity'];?></td>
          <td><?=$item['threshold'];?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
 </div>
</div>

<?php 
include 'includes/footer.php';
?>