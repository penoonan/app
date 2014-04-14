<?php

use \Sketch\Metabox\BaseMetabox;

class HelloMetabox extends BaseMetabox {
    protected
        $id = 'hello_metabox',
        $post_type = 'hello_post_type',
        $callback_controller = 'hello@metabox'
    ;
}