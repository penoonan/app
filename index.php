<?php
//ini_set('display_errors', -1); error_reporting(E_ALL);

$app = require_once 'app/bootstrap/app.php';

$app->make('Hello\Hello');
$app->make('Hello\Submenus\HelloSubmenu');

$hello_post_type = $app->make('HelloPostType');
$hello_post_type->addMetabox($app->make('HelloMetabox'));