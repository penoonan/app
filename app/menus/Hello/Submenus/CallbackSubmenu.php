<?php

namespace Hello\Submenus;

use Sketch\WpSubmenuAbstract;
class CallbackSubmenu extends WpSubmenuAbstract {

    public
      $parent_slug = 'sketch_hello_world',
      $page_title = 'Callback Submenu!',
      $menu_title = "Callback!",
      $capability = "edit_themes",
      $menu_slug  = "sketch_hello_callback";

} 