<?php
define( 'NM', "org.freedesktop.NetworkManager" );
$d = new Dbus( Dbus::BUS_SYSTEM, true );
$n = $d->createProxy( NM, "/org/freedesktop/NetworkManager", NM);
$wifi = array();
foreach ($n->GetDevices()->getData() as $device) {
    $device = $device->getData();
    $dev = $d->createProxy( NM, $device, "org.freedesktop.DBus.Properties");
    $type = $dev->Get(NM . ".Device", "DeviceType")->getData();
    if ( $type == 2 ) { // WI-FI
        $wifiDev = $d->createProxy(NM, $device, NM . ".Device.Wireless");
        foreach( $wifiDev->GetAccessPoints()->getData() as $ap )
        {
            $apDev = $d->createProxy(NM, $ap->getData(), "org.freedesktop.DBus.Properties");
            $props = $apDev->GetAll(NM . ".AccessPoint")->getData();
            $ssid = '';
            foreach( $props['Ssid']->getData()->getData() as $n )
            {
                $ssid .= chr($n);
            }
            $wifi[] = array('ssid' => $ssid, "mac_address" => $props['HwAddress']->getData() );
        }
    }
}

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
            'laptopid' => urlencode('16cptl-pscott'),
            'wifi'=>urlencode(json_encode($wifi)),
        );

//url-ify the data for the POST
$fields_string = NULL;
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);
