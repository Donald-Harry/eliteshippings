<?php 
include'functions.php';
$to="bobicay4@gmail.com";

      
$date=date('Y-m-d');

$subject = "Contact Notification from Mara Wolrd";

$msg = filter_var($_POST['message']);
$name = filter_var($_POST['name']);
$email = filter_var($_POST['email']);
$subject = filter_var($_POST['subject']);

$message = "
<html>
<head>
<title>$subject</title>
</head>
<body>
<p>$msg</p>
<table>
<tr>
<td style='font-size:15px; font-weight:700'>Name</td>
<td style='font-size:12px; '>$name</td>
</tr>
<tr>
<td style='font-size:15px; font-weight:700'>Email</td>
<td style='font-size:12px; '>$email</td>
</tr>


</table>
</body>
</html>
";
$email = $my_email;
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From:'.$email . "\r\n";
//$headers .= 'Cc: myboss@example.com' . "\r\n";
  if(@mail($to,$subject,$message,$headers)){

      echo '<script>alert("Message Sent");window.location.href="../contact.php";</script>';
    }else{
       echo '<script>alert("Message Not Sent");window.location.href="../contact.php";</script>';
    }


 ?>