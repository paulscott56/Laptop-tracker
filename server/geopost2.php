<?php

$wifi = $_POST['wifi'];
//var_dump($wifi);
$wifi = json_decode($wifi);
$request = array( 'version' => '1.1.0', 'host' => 'myurl.com', 'wifi_towers' => $wifi );
$c = curl_init();
curl_setopt( $c, CURLOPT_URL, 'https://www.google.com/loc/json' );
curl_setopt( $c, CURLOPT_POST, 1 );
curl_setopt( $c, CURLOPT_POSTFIELDS, json_encode( $request ) );
curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
$result = json_decode( curl_exec( $c ) )->location;

// OK now we send it to the server
//set POST variables
$url = 'http://127.0.0.1/junk/geo/geopost.php';
$fields = array(
            'lat'=>urlencode($result->latitude),
            'lon'=>urlencode($result->longitude),
            'accuracy'=>urlencode($result->accuracy),
            'laptopid' => urlencode($wifi['laptopid']),
            'wifi'=>urlencode(json_encode($wifi)),
        );

//var_dump($fields);

// connect
$m = new Mongo();

// select a database
$db = $m->laptoptrack;

// select a collection (analogous to a relational database's table)
$collection = $db->lappoints;

// add a record
$obj = array( "loc" => array("lon" => floatval($fields['lon']), "lat" => floatval($fields['lat'])), "lon" => floatval($fields['lon']), "lat" => floatval($fields['lat']), "accuracy" => intval($fields['accuracy']), "laptopid" => $fields['laptopid'], "wifi" => json_encode($wifi));
$collection->ensureIndex(array('loc' => "2d"));
$collection->insert($obj, array('safe'=>true)); 

