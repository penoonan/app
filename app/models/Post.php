<?php

use Sketch\Wp\WpBaseModel;

class Post extends WpBaseModel {

    public function all()
    {
        return $this->wp->get_posts();
    }

} 