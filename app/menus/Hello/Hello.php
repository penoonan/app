<?php

namespace Hello;

class Hello extends \Sketch\WpMenuAbstract {

    public
      $page_title = 'Hello!',
      $menu_title = "Hello World!",
      $capability = "edit_themes",
      $menu_slug  = "sketch_hello_world";

}