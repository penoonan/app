<?php

use \Sketch\CustomPostType\BaseCustomPostType;

class HelloPostType extends BaseCustomPostType {

    protected
        $post_type = 'hello_post_type',
        $name = "Hello",
        $show_ui = true,
        $show_in_menu = true;
} 