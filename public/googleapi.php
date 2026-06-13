<?php

    header("Cache-Control: private, max-age=86400");
    header("Expires: ".gmdate('r', time()+86400));
    $query = 'surat';
    $apikey = 'AIzaSyD8GdePwqtn_AUN98KKj8eddsxOwND3Wkg';
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?key='.$apikey.'&types=geocode&sensor=true&radius=12000&components=country:IN&input='.urlencode($query);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data1 = curl_exec($ch);
    curl_close($ch);
    $details = json_decode($data1, true);
    header("Content-Type: application/json");
    $arr = array();
    foreach($details['predictions'] as $key=>$row) {
        $arr[] = $row['description'];
    }
    $json['result'] = $arr;
    echo json_encode($json);
    
?>