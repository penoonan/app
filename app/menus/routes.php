<?php

$app['router']->get(array('page' => 'sketch_hello_world'), 'hello@index');
$app['router']->get(array('page' => 'sketch_hello_submenu'), 'hello@submenu');

// Use a callback!
$app['router']->get('page=sketch_hello_callback', function() use($app) {
    $data = array(
        'page' => $app['request']->query->get('page')
    );
    echo $app['template']->render('callback', $data);
});