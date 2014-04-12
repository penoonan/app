<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once 'util/helpers.php';

DEFINE('ARCSTONE_SKETCH_BASE_DIR', __DIR__);
DEFINE('ARCSTONE_SKETCH_BASE_URI', content_url());

$app = new \Illuminate\Container\Container();

$app->singleton('Symfony\Component\HttpFoundation\Request', function() {
      return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
});

$app->bind('League\Plates\Template', function() use ($app) {
      $engine = new League\Plates\Engine( __DIR__.'/../views');
      $engine->loadExtension(new \League\Plates\Extension\URI($app->make('Symfony\Component\HttpFoundation\Request')));
      $engine->loadExtension(new \League\Plates\Extension\Asset(__DIR__.'/app/public', true));
      $engine->loadExtension($app->make('Sketch\TemplateHelpers'));
      $view_folders = glob(__DIR__.'/../views/*' , GLOB_ONLYDIR);
      foreach ($view_folders as $folder) {
          $engine->addFolder(basename($folder), $folder);
      }

      return $engine->makeTemplate();
  });

$app['template'] = $app->make('League\Plates\Template');

$app->bind('Sketch\Dispatcher', function() use($app) {
      return new \Sketch\Dispatcher($app, $app->make('League\Plates\Template'));
});

//Register all them routes
require_once __DIR__ . '/../routes.php';
$app->bind('Sketch\RouterInterface', function() use($app) { return $app['router']; });

// return the app!
return $app;