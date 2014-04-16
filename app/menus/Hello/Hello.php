<?php

namespace Hello;
use Sketch\Menu\WpMenuAbstract;

class Hello extends WpMenuAbstract {

    public
      $page_title = 'Hello!',
      $menu_title = "Hello World!",
      $capability = "edit_themes",
      $menu_slug  = "sketch_hello_world";

}