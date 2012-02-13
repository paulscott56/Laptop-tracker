<?php
//var_dump($_POST);
$lon = $_POST['lon'];
$lat = $_POST['lat'];
$accuracy = $_POST['accuracy'];
$wifi  = $_POST['wifi'];
$wifi = json_decode($wifi);
$laptopid = $_POST['laptopid'];
// echo $lon, $lat, $accuracy;


// connect
$m = new Mongo();

// select a database
$db = $m->laptoptrack;

// select a collection (analogous to a relational database's table)
$collection = $db->lappoints;

// add a record
$obj = array( "loc" => array("lon" => floatval($lon), "lat" => floatval($lat)), "lon" => floatval($lon), "lat" => floatval($lat), "accuracy" => intval($accuracy), "laptopid" => $laptopid, "wifi" =>  $wifi);
$collection->ensureIndex(array('loc' => "2d"));
$collection->insert($obj, array('safe'=>true)); 
