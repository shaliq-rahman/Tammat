<?php
date_default_timezone_set('Asia/Kolkata');
//error_reporting('E_NOTICE ^ E_ALL');
include_once 'deal_function.php'; 
$con = new DB_con();
header("Content-type: application/json; charset=iso-8859-1");
$inputdata = file_get_contents('php://input');

$data = json_decode($inputdata, TRUE);
$action = $data['action'];

if($action=='login')
{
    $insert = $con->login("users",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}


if($action=='register')
{
    $insert = $con->register("users",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='account')
{
    $insert = $con->account("users",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='profile_data_fetch')
{
    $insert = $con->profile_data_fetch("users",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='change_password')
{
    
    $insert = $con->change_password("users",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='add_post')
{
    $insert = $con->add_post("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='add_post_image')
{
    $insert = $con->add_post_image("pictures",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='payment')
{
    $insert = $con->payment("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action=='latest_ads')
{
    $insert = $con->latest_ads("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}


if($action =='logout')
{
    $insert = $con->logout("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action =='category')
{
    $insert = $con->category($data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}


if($action =='countries')
{
    $insert = $con->countries("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action =='currencies')
{
    $insert = $con->currencies("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}

if($action =='languages')
{
    $insert = $con->languages("posts",$data);
    $msg = array("result" => $insert);
    header('content-type: application/json');
    echo json_encode($msg,true);
}


?>