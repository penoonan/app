<?php

namespace Sketch;

abstract class WpMenuAbstract extends WpBaseMenuAbstract {

    public
        $page_title = 'Menu',
        $menu_title = "Menu Title",
        $capability = "edit_themes",
        $menu_slug  = "menu_slug";

    public function addMenu()
    {
        $this->wp->add_menu_page(
          $this->page_title,
          $this->menu_title,
          $this->capability,
          $this->menu_slug,
          array($this->router, 'resolve'),
          $this->icon_url,
          $this->position
        );
    }
} 