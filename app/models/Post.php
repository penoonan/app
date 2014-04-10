<?php

class Post extends \Sketch\WpBaseModel {

    public function all()
    {
        return $this->wp->get_posts();
    }

} 