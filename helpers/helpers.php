<?php

function display_errors($errors){
    $display = '<ul class="bg-danger" id="err">';
    foreach ($errors as $error) {
        $display .='<li class="text-danger">'.$error.'</li>';
        break;
    }
    $display .='</ul>';
    return $display;}

function sanitize($dirty){
    return htmlentities($dirty, ENT_QUOTES, "UTF-8");
}

function money($number){
 return '$'.number_format($number,2);
}

function login($user_id){
    $_SESSION['DJuser'] = $user_id;
    global $db;
    $date = date("Y-m-d H:i:s");
    $db->query("UPDATE users SET last_login = '$date' WHERE id = $user_id;");
    $_SESSION['success_flash'] = 'You are now logged in!';
    header('Location: index.php');
}

function is_logged_in(){
    if(isset($_SESSION['DJuser']) && $_SESSION['DJuser'] > 0){
        return true;
    }
    return false;
}

function login_error_redirect($url = 'login.php'){
    $_SESSION['error_flash'] = 'User must be logged in to access page data';
    header('Location: '.$url);
}

function permission_error_redirect($url = 'login.php'){
    $_SESSION['error_flash'] = 'You do not have Permission to access page data';
    header('Location: '.$url);
}


function has_permission($permission ='admin'){
     global $user_data; 
     $permissions = explode(',', $user_data['permissions']);
     if (in_array($permission,$permissions,true)) {
         return true;
     }
     return false;
}

function pretty_date($date){
    return date("M d, Y h:i A",strtotime($date));
}
 function pwd_check($password){
     if (!isset($_GET['edit'])) {
       if(strlen($password) < 6){
        global $errors;
        $errors[] = 'Password must be at least 6 Characters';
       }
     }else {
       if ($password ==='0') {
         global $errors;
         $errors[] = 'Password must be at least 6 Characters';
        }
         if (!empty($password) && strlen($password) < 6) {
         global $errors;
         $errors[] = 'Password must be at least 6 Characters'; }
     }
 }
   function get_cat($child_id){
       global $db;
       $id = sanitize($child_id);
       $sql ="SELECT p.id AS 'pid', p.category AS 'parent', c.id AS 'cid', c.category AS 'child'
              FROM categories c
              INNER JOIN categories p
              ON c.parent = p.id
              WHERE c.id ='$id'";
       $query = $db->query($sql);
       $category = $query->fetch_assoc();
       return $category;
 }
    function sizesToArray($string){
        $sizesArray = explode(',',$string);
        $returnArray = array();
        foreach ($sizesArray as $size) {
            $s = explode(':',$size);
            $returnArray[] = array('size' =>$s[0], 'quantity' => $s[1],'threshold' => $s[2]);
        }
        return $returnArray;
    }
        function sizesToString($sizes){
            $sizeStr = '';
            foreach($sizes as $size){
                $sizeStr .=$size['size'].':'.$size['quantity'].':'.$size['threshold'].',';
            }
            $trimed = rtrim($sizeStr, ',');
            return $trimed;
        }
        function saveP($lP,$oP){
            $rem =  $lP - $oP;
            $p = ($rem * 100) / $lP;
            $p = round($p, 0);
            return $p;
        }
        ?>