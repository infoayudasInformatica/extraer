<?php
require_once './tools/BBDD.php';
$BBDD = new BBDD();

$BBDD->borrarTabla();

echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=./index.php">';die;
