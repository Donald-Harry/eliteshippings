<?php  
session_start();
include 'php/functions.php';

	if ($_POST['submit'] == "remove_member") {
	
        $id = $_POST['id'];

            $sql = "DELETE FROM `info` WHERE `id` = '$id'";
            
			$query = $connect->query($sql);
			
			echo'<script>
				window.history.back();
			</script>';

	}
	
	?>