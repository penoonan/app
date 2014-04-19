<?php

use \Sketch\CustomPostType\BaseCustomPostType;

class HelloPostType extends BaseCustomPostType {

    protected
        $post_type = 'hello_post_type',
        $args = array(
            'show_ui' => true,
            'show_in_menu' => true
        ),
        $labels = array(
            'menu_name' => 'Hello',
            'all_items' => 'All Hello Posts',
            'name' => 'Hello Posts'
        );
}