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

    // Use a callback!
    $router->get('page=sketch_hello_submenu&action=test_callback', function() use($app) {
          echo $app['template']->render('callback');
    });

    $router->get(array('page' => 'sketch_hello_submenu'), 'hello@submenu');



    /*--------------
     | END ROUTES! |
     *-------------*/

    return $router;
};