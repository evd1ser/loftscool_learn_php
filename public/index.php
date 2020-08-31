<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

require '../Base/init.php';

$app = new \Base\Application();
$app->run();
