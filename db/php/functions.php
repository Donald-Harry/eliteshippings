<?php 
session_start();
$host = "localhost";
$user = "u787607796_swiftexpress";
$password = 'Swiftexpress1234@';
$db_name = "u787607796_swiftexpress";
    $db = mysqli_connect($host, $user, $password, $db_name);  
    if(mysqli_connect_errno()) {  
        die("Failed to connect with MySQL: ". mysqli_connect_error());  
    }
$site_name = "swiftexpresses";
$site_url = 'www.swiftexpresses.online';
$nav_config_pt = '/logistic/';

$my_email = "support@swiftexpresses.online";

 function sanitize(&$input) { 
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $input[$var] = sanitize($val);
        }
    }
    else {
      
    }

    return $input;
   }

function cleanInput($input) {
 
  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );
 
    $output = preg_replace($search, '', $input);
    return htmlentities(strip_tags($output));
  }


 function getId(){
          global $db;
          if(isset($_SESSION['tracking_id'])){
               $tracking_id = $_SESSION['tracking_id'];
               $sql = "SELECT tracking_id FROM info WHERE tracking_id ='$tracking_id'";
               $run = $db->query($sql);
               $info = $run->fetch_assoc();
               return $info['tracking_id'];
          }else{
              return "";
          }
     }
  function get($column,$id=''){  
    global $db;
    if($id == '')
      $tracking_id = getId();
    else
       $tracking_id = $id;
     $sql = "SELECT ".$column." FROM info WHERE tracking_id = '$tracking_id'";
     $run = $db->query($sql);
     if(!$run){
      return $db->error;
     }
     $info = $run->fetch_assoc();
     $run->num_rows;
     if($column != '*')
       return $info[$column];
     else
         return $info;
  }

 ?>