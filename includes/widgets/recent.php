

<h3 class="text-center">Popular Items</h3>
  <?php 
  $tranQ = $db->query("SELECT * FROM cart WHERE paid = 1 ORDER BY id DESC LIMIT 10");
  $results = [];
  while($row = $tranQ->fetch_assoc()){
  $results[] = $row;
  }
  $row_count = $tranQ->num_rows;
  $used_ids = [];
  
  for ($i=0; $i < $row_count; $i++) { 
    $json_items = $results[$i]['items'];
    $items = json_decode($json_items,true);
    foreach($items as $item){
      //echo $item['id'].'to';
     // print_r ($used_ids); 
     if(!in_array($item['id'], $used_ids)){
       $used_ids[] = $item['id'];
      }
    }
  }
  ?>
  <div>
   <table class="table table-condensed"  id="recent_widget">
    <?php
    foreach ($used_ids as $id) :
    $productQ = $db->query("SELECT id,title FROM products WHERE id = '{$id}'");
    $product = $productQ->fetch_assoc();
    ?>
    <tr>
      <td>
       <?=substr($product['title'],0,16); ?>
      </td>
      <td><a class="text-primary" onclick="detailsmodal('<?=$id;?>')">View</a></td>
    </tr>
      
  <?php endforeach;

    ?>
  </table>
  </div>