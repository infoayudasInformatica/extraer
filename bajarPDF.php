<?php

$file = $_GET['url'];


$nombreExplode = explode('/',$file);
$nombre = $nombreExplode[count($nombreExplode)-1];


$path = dirname(__FILE__)."\PDF\\".$nombre;

$ch = curl_init($file);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($ch);

curl_close($ch);

$OK = file_put_contents($path, $data);

if($OK){
    echo "correcto<br/>";
}else{
    echo "incorrecto<br/>";
}