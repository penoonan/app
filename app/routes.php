<?php
//Configure the router
$app['router'] = function($app) {
    $router = new \Sketch\QueryStringRouter(
      $app->make('Sketch\Dispatcher'),
      $app->make('Symfony\Component\HttpFoundation\Request')
    );

    /*---------------*
     | START ROUTES! |
     *---------------*/

    $router->get(array('page' => 'sketch_hello_world'), 'hello@index');
    $router->get(array('page' => 'sketch_hello_submenu'), 'hello@submenu');

    /*--------------
     | END ROUTES! |
     *-------------*/

    return $router;
};

$app->bind('Sketch\RouterInterface', function() use($app) { return $app['router']; });