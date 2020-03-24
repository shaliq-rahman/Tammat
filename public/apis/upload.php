<?php
date_default_timezone_set('Asia/Kolkata');

$d = mysqli_connect('localhost', 'dealnotd_dealnotdeal', '$ZuD0@Rz#B+{', 'dealnotd_dealnotdeal') or die(mysqli_connect_error());

//print_r($_POST);

        if ($_POST['action'] == "add_post_image") {

			$query=mysqli_query($d, "SELECT * FROM posts ORDER BY id DESC LIMIT 1")or die(mysqli_error($d));
            $row=mysqli_fetch_array($query);							
			
			
			$name = $_FILES['pictures']['name'];
			$tmpname = $_FILES['pictures']['tmp_name'];
			for($i=0; $i<count($name); $i++)
			{
			$nname = time().'-'.$name[$i];
			$path = "../storage/files/";
			move_uploaded_file($path.$tmpname[$i],$nname);
			
			$query_post_img = mysqli_query($d, "INSERT INTO pictures (post_id,filename,position) VALUES ('".$row['id']."','".$nname."','1')")or die(mysqli_error($d));
			
			}
			
			
			
			
			
			if($query_post_img){
				$msg = array('msg' => 'The pictures have been updated');
				echo json_encode($msg);	
			}
			else{
				$msg  = array('msg' => 'Data Not Insert');
				echo json_encode($msg);
			}
        }
        else {
            $msg = array('msg' => 'Post Action','statuscode' => '203');
            echo json_encode($msg);
        }
 

?>