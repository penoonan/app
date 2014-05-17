<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once 'helpers.php';

//Instantiate the Application
$app = new \Sketch\Application();

//Register the Plates Service Provider
$app->register(new Sketch\Providers\PlatesServiceProvider(), array(
    'template.dir' => __DIR__ . '/views',
    'template.asset_dir' => __DIR__.'/public'
));

//Register all them routes
require_once __DIR__ . '/menus/routes.php';

// return the app!
return $app;

