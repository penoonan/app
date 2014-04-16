<?php

namespace Hello\Submenus;

use Sketch\Menu\WpSubmenuAbstract;

class HelloSubmenu extends WpSubmenuAbstract {

    public
        $parent_slug = 'sketch_hello_world',
        $page_title = 'Hello Submenu!',
        $menu_title = "Submenu!",
        $capability = "edit_themes",
        $menu_slug  = "sketch_hello_submenu";

} 