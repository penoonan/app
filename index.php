<?php
$app = require_once 'app/bootstrap/app.php';

$app->make('Hello\Hello');
$app->make('Hello\Submenus\HelloSubmenu');