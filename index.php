<?php
$app = require_once 'app/bootstrap.php';

// Add our sample menus and submenus
$app->make('Hello\Hello');
$app->make('Hello\Submenus\HelloSubmenu');
$app->make('Hello\Submenus\CallbackSubmenu');

// Add our sample post type, with a metabox and a taxonomy
$app->make('HelloPostType')
    ->addMetabox($app->make('HelloMetabox'))
    ->addTaxonomy($app->make('HelloTaxonomy'));