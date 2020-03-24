<?php
//error_reporting('E_NOTICE ^ E_ALL');
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dealnotd_dealnotdeal');
date_default_timezone_set('Asia/Kolkata');
class DB_con {
    protected $conn;
    function __construct() {
        $this->conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME) or die(mysqli_connect_error());
		
    }


/**********************Login API starts here**********************/
    public function login($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "login") == 0) {
            $query_checklogin = mysqli_query($this->conn, "Select * from $ssTableName where email='".$asFields['email']."' and password = '" . $asFields['password'] . "' ")or die(mysqli_error());
            $matchFound = mysqli_num_rows($query_checklogin);
            if ($matchFound>0) {
                $fetch = mysqli_fetch_array($query_checklogin);
$query_select = mysqli_query($this->conn, "SELECT *
FROM $ssTableName where email='".$asFields['email']."' and password='".$asFields['password']."' ") or die(mysqli_error($this->conn));
                    $fetch = mysqli_fetch_assoc($query_select);
					unset($fetch['password']);
					$fetch['msg']='Login Successfully';
				    $fetch['statuscode']='200';
                    return $fetch;
                
            } else {
                $msg = array('msg' => 'Record does not exist','statuscode' => '204');
                return $msg;
            }
        } else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Login API ends here**********************/

/**********************Register API starts here******************/
     public function register($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "register") == 0) {
			$firstname = $asFields['first_name'];
			$lastname = $asFields['last_name'];
			$phone = $asFields['phone'];
			$country_code = $asFields['country_code'];
			$city = $asFields['city'];
			$email = $asFields['email'];
			$username = $asFields['username'];
			$password = $asFields['password'];
           	$query_check_email = mysqli_query($this->conn,"select * from $ssTableName where email='".$email."'");
			if (mysqli_num_rows($query_check_email) > 0) {
			$row = mysqli_fetch_assoc($query_check_email);
			if ($email==$row['email'])
			{
			     return $msg = array('msg' => 'Email already exists');
			}
			}
			else{
			$query_register = mysqli_query($this->conn, "INSERT INTO $ssTableName (first_name, last_name, phone, country_code, city, email, username, password) VALUES ('".$firstname."', '".$lastname."', '".$phone."', '".$country_code."', '".$city."', '".$email."', '".$username."', '".$password."')");
			if($query_register){
			 return	$msg = array('msg' => 'Insert Data succesfully');			
			}
			else{
				return $msg  = array('msg' => 'Data Not Insert');
			}	
			}
			
        
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Register API ends here**********************/



/**********************Profile Update API starts here******************/
     public function account($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "account") == 0) {
			$id = $asFields['id'];
			$gender_id = $asFields['gender_id'];
			$first_name = $asFields['first_name'];
			$lastname = $asFields['last_name'];
			$username = $asFields['username'];
			$email = $asFields['email'];
			$phone = $asFields['phone'];
			$dob = $asFields['dob'];
			$country_code = $asFields['country_code'];
			$state = $asFields['state'];
			$city = $asFields['city'];
			$zipcode = $asFields['zipcode'];
			$address = $asFields['address'];
			$user_type_id = $asFields['user_type_id'];
			$query_account = mysqli_query($this->conn, "update $ssTableName set gender_id='$gender_id',first_name='$first_name',last_name='$lastname',username='$username',email='$email',phone='$phone',dob='$dob',country_code='$country_code',state='$state',city='$city',zipcode='$zipcode',address='$address',user_type_id='$user_type_id' where id='$id'");
			if($query_account){
			 return	$msg = array('msg' => 'Update Data succesfully');			
			}
			else{
				return $msg  = array('msg' => 'Data Not Update');
			}	       
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Profile Update API ends here**********************/


/**********************Profile Fetch Data API starts here******************/
     public function profile_data_fetch($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "profile_data_fetch") == 0) {
			$id = $asFields['id'];
			$query=mysqli_query($this->conn, "select * from users where id='$id'");
            $row=mysqli_fetch_array($query);
	        return	$product_arr = array(
                "id" =>  $row['id'],
                "gender_id" =>$row['gender_id'],
				"first_name" =>$row['first_name'],
				"last_name" =>$row['last_name'],
				"username" =>$row['username'],
				"email" =>$row['email'],
				"phone" =>$row['phone'],
				"dob" =>$row['dob'],
				"country_code" =>$row['country_code'],
				"state" =>$row['state'],
				"city" =>$row['city'],
				"zipcode" =>$row['zipcode'],
				"address" =>$row['address'],
				"user_type_id" =>$row['user_type_id'],			
                );
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Profile Fetch Data API ends here**********************/

/**********************Change Passwod API starts here******************/
     public function change_password($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "change_password") == 0) {
			$id = $asFields['id'];
			$password = $asFields['password'];
			$query_change = mysqli_query($this->conn, "update $ssTableName set password='$password' where id='$id'");
			if($query_change){
			 return	$msg = array('msg' => 'Change Passwod succesfully');			
			}
			else{
				return $msg  = array('msg' => 'Data Not Passwod');
			}	       
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Change Passwod API ends here**********************/


/**********************Add Post API starts here******************/
    public function add_post($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "add_post") == 0) {
			$category_id = $asFields['category_id'];
			$post_type_id = $asFields['post_type_id'];
			$title = $asFields['title'];
			$description = $asFields['description'];
			$price = $asFields['price'];
			$city_name = $asFields['city_name'];
			$email = $asFields['email'];
			$phone = $asFields['phone'];	
			$id = $asFields['id'];
			$query=mysqli_query($this->conn, "select * from users where id='$id'");
            $row=mysqli_fetch_array($query);							
			$query_post = mysqli_query($this->conn, "INSERT INTO $ssTableName (country_code,user_id,category_id, post_type_id, title, description, price, city_name, email, phone, created_at, updated_at) VALUES ('".$row['country_code']."','".$row['id']."','".$category_id."', '".$post_type_id."', '".$title."', '".$description."', '".$price."', '".$city_name."', '".$email."', '".$phone."', NOW(), NOW())");
			
			if($query_post){
			 return	$msg = array('msg' => 'Insert Data succesfully');			
			}
			else{
				return $msg  = array('msg' => 'Data Not Insert');
		    }        
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Add Post API ends here**********************/

/**********************Add Post Image API starts here******************/
    public function add_post_image($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "add_post_image") == 0) {

			$query=mysqli_query($this->conn, "SELECT * FROM posts ORDER BY id DESC LIMIT 0 , 1");
            $row=mysqli_fetch_array($query);							
			foreach($asFields['pictures'] ['name'] as $key=>$value){
				$name= $asFields['pictures'] ['name'] [$key];
				$tmp= $asFields['pictures'] ['tmp_name'] [$key];
				$exp=explode('.',$name);
				$end_ext=$exp[1];
				$num=rand(0,200);
				$new_name="storage/$num.$end_ext";
					if(move_uploaded_file($tmp,$store))
					{
							$query_post_img = mysqli_query($this->conn, "INSERT INTO $ssTableName (post_id,filename,created_at,updated_at) VALUES ('".$row['id']."','".$new_name."', '".NOW()."', '".NOW()."')")or die(mysqli_error($this->conn));
					}	
			}
			if($query_post_img){
				return	$msg = array('msg' => 'Insert Data succesfully');	
			}
			else{
				return $msg  = array('msg' => 'Data Not Insert');
			}
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Add Post Image API ends here**********************/	

/**********************Add Post Payment starts here******************/
    public function payment($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "payment") == 0) {
			$user_id = $asFields['id'];
			$from_email = $asFields['from_email'];
			$from_phone = $asFields['from_phone'];	
			$query=mysqli_query($this->conn, "SELECT * FROM posts where user_id ='$user_id' ORDER BY id DESC LIMIT 1");
            $row=mysqli_fetch_array($query);	
            $post_id = $row['id']; 
			$query_update = mysqli_query($this->conn, "update posts set premium_email='".$from_email."', premium_phone='".$from_phone."' where id='".$row['id']."'");
			if($query_update){
			 return	$msg = array('msg' => 'Your add has been created.');			
			}
			else{
				return $msg  = array('msg' => 'Data Not Insert');
		    }	
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************Add Post Payment ends here**********************/


/**********************logout here******************/
    public function logout($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "logout") == 0) {
            session_start();
			$sess_des = session_destroy();
			if($sess_des){
			return	$msg = array('msg' => 'You have been logged out. See you soon.');
			}	
			else{
			return	$msg = array('msg' => 'Session not destory.');	
			}			
  	
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }
/**********************logout here**********************/


/**********************latest_ads here****************************/
    public function latest_ads($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "latest_ads") == 0) {

			$query=mysqli_query($this->conn, "SELECT * FROM posts JOIN pictures ON posts.id = pictures.id");
			$products_arr=array();
           while($row=mysqli_fetch_array($query)){          
             $product_item =  array("id" => $row['id'],"country_code" => $row['country_code'],"category_id" => $row['category_id'], "filename" => "www.dealnotdeal.com/storage/".$row['filename'],"contact_name" => $row['contact_name'],"created_at" => date('d-m-y h:i:s A',strtotime($row['created_at'])),"country_code" => $row['country_code'],"price" => $row['price']);
            array_push($products_arr, $product_item);
           }
           echo json_encode($products_arr);
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }

/**********************latest_ads here**********************/

/**********************countries here****************************/

    public function countries($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "countries") == 0) {

			$query=mysqli_query($this->conn, "SELECT * FROM countries");
			//$countries_arr=array();
            while($row=mysqli_fetch_assoc($query)){          
				$countries_arr[] = $row;
            }
            return $countries_arr;
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }

/**********************countries end here**********************/

 /**********************currencies here****************************/

    public function currencies($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "currencies") == 0) {

			$query=mysqli_query($this->conn, "SELECT * FROM currencies");
			$currencies_arr=array();
            while($row=mysqli_fetch_array($query)){          
				$currencies_arr[] = array(
				    
				    "id" => $row['id'],
				    "code" => $row['code'],
				    
				);
            }
            return $currencies_arr;
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }

/**********************currencies end here**********************/


 /**********************languages here****************************/

    public function languages($ssTableName, $asFields) {
        if (strcmp($asFields['action'], "languages") == 0) {

			$query=mysqli_query($this->conn, "SELECT * FROM languages");
			$languages_arr=array();
            while($row=mysqli_fetch_array($query)){          
				$languages_arr[] = array(
				    
				    "id" => $row['id'],
				    "abbr" => $row['abbr'],
				    "locale" => $row['locale']
				    
				);
            }
            return $languages_arr;
        }
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    }

/**********************languages end here**********************/


/**********************View Category starts here****************************/
    public function category($asFields) {
        if (strcmp($asFields['action'], "category") == 0) {
		
		$qryse = mysqli_query($this->conn, "select * from categories where translation_lang='".$asFields['translation_lang']."' and parent_id='0' order by name asc ");
		//$fetch1 = array();
		$i=0;
		while($rowse = mysqli_fetch_assoc($qryse))
		{
		$fetch['categories'][$i] = $rowse;
		$qryse1 = mysqli_query($this->conn, "select * from categories where translation_lang='".$asFields['translation_lang']."' and parent_id='".$rowse['id']."' order by name asc ");
		while($rowse1 = mysqli_fetch_assoc($qryse1))
		{
		$fetch1[] = $rowse1;
		}
		$fetch['categories'][$i]['subcategories'] = $fetch1;
		$i++;
		}
		return $fetch;
		}
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    } 
/**********************View Category end starts here**********************/  
    public function cat($asFields) {
        if (strcmp($asFields['action'], "category") == 0) {
		
		$qryse = mysqli_query($this->conn, "select * from categories where translation_lang='".$asFields['translation_lang']."' and parent_id='0' order by name asc ");
		//$fetch1 = array();
		$i=0;
		while($rowse = mysqli_fetch_assoc($qryse))
		{
		$fetch['categories'][$i] = $rowse;
		$qryse1 = mysqli_query($this->conn, "select * from categories where translation_lang='".$asFields['translation_lang']."' and parent_id='".$rowse['id']."' order by name asc ");
		while($rowse1 = mysqli_fetch_assoc($qryse1))
		{
		$fetch1[] = $rowse1;
		}
		$fetch['categories'][$i]['subcategories'] = $fetch1;
		$i++;
		}
		return $fetch;
		}
        else {
            $msg = array('msg' => 'Something Error','statuscode' => '203');
            return $msg;
        }
    } 
}
?>
