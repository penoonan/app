<?php
//ini_set('display_errors', -1);
//error_reporting(-1);

$app = require_once 'app/bootstrap/app.php';

$app->make('Hello\Hello');
$app->make('Hello\Submenus\HelloSubmenu');