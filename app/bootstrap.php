<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once 'helpers.php';

//$app = new \Illuminate\Container\Container();
//
//$app['request'] = $app->share(function() {
//    return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
//});
//
//$app['template'] = $app->share(function() use ($app) {
//      $engine = new League\Plates\Engine( __DIR__.'/views');
//      $engine->loadExtension(new \League\Plates\Extension\URI($app['request']));
//      $engine->loadExtension(new \League\Plates\Extension\Asset(__DIR__.'/app/public', true));
//      $engine->loadExtension($app->make('Sketch\TemplateHelpers'));
//      $view_folders = glob(__DIR__.'/views/*' , GLOB_ONLYDIR);
//      foreach ($view_folders as $folder) {
//          $engine->addFolder(basename($folder), $folder);
//      }
//      return $engine->makeTemplate();
//});
//
//$app->bind('Sketch\Dispatcher', $app->share(function() use ($app) {
//    return new \Sketch\Dispatcher($app);
//}));
//
////Configure the router
//$app['router'] = $app->share(function() use ($app) {
//      return new \Sketch\QueryStringRouter (
//        $app->make('Sketch\Dispatcher'),
//        $app['request']
//      );
//  });
//$app->bind('Sketch\RouterInterface', function() use($app) { return $app['router']; });

$app = new \Sketch\Application();

$app->register(new Sketch\Providers\PlatesServiceProvider(), array(
    'template.dir' => __DIR__ . '/views',
    'template.asset_dir' => __DIR__.'/public'
));

//Register all them routes
require_once __DIR__ . '/menus/routes.php';

// return the app!
return $app;

