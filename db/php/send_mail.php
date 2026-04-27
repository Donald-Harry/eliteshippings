   <?php

$to="info@westernmaritimeconstruction.com";

        
         $email = $_POST['email'];
          $First = $_POST['First'];
           $Last = $_POST['Last'];
            $Phone = $_POST['Phone'];
             $Address = $_POST['Address'];
             $subject = $_POST['subject'];
             $comment = $_POST['comment'];
            $date=date('Y-m-d');

$subject = "Contact Notification from $email";

$message = "
<html>
<head>
<title>Western Maritime Construction</title>
</head>
<body>
<p>This email contains information about $subject</p>
<table>




<tr>
<td style='font-size:15px; font-weight:700'>User Email</td>
<td style='font-size:12px; '>$email</td>
</tr>
<tr>
<td style='font-size:15px; font-weight:700'>Full Name</td>
<td style='font-size:12px; '>$First $Last</td>
</tr>
<tr>
<td style='font-size:15px; font-weight:700'>Phone Number</td>
<td style='font-size:12px; '>$Phone</td>
</tr>
<tr>
<td style='font-size:15px; font-weight:700'>Message</td>
<td style='font-size:12px; '>$comment</td>
</tr>
<tr>
<td style='font-size:15px; font-weight:700'>Date</td>
<td style='font-size:12px; '> $date </td>
</tr>

</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From:'.$email . "\r\n";
//$headers .= 'Cc: myboss@example.com' . "\r\n";
if(@mail($to,$subject,$message,$headers))
{

    echo 'Submitted successfully';
 
}else{
 echo 'An error occured';
}

 ?>