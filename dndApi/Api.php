<?php
defined('BASEPATH') OR exit('No direct script access allowed');
function category()
  {
    $response =array();
    $response['status'] = 1;
    $response['message'] = "Success";
    print json_encode($response);
  }
?>