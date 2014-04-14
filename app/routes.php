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
    // Use a callback!
    $router->get('page=sketch_hello_callback', function() use($app) {
          $data = array(
            'page' => $app['request']->query->get('page')
          );
          echo $app['template']->render('callback', $data);
      });



    /*--------------
     | END ROUTES! |
     *-------------*/

    return $router;
};