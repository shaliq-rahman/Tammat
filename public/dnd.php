<?php
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dealnotd_dealnotdeal');
date_default_timezone_set('Asia/Kolkata');
class DB_con {
    protected $conn;
    function __construct() {
        $this->conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME) or die(mysqli_connect_error());
		//echo $conn;
    }
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
} 