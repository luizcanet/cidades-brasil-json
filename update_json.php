<?php

$updater_file = 'LuizCanet' . DIRECTORY_SEPARATOR . 'CidadesBrasil' . DIRECTORY_SEPARATOR . 'Updater.php';
require_once $updater_file;

use LuizCanet\CidadesBrasil\Updater as Updater;

$updater = new Updater();
$updater->loadCities();
$updater->saveToJson();

?>
