<?php

namespace Sketch;

class WpDbWrapper {

    protected $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->wpdb, $method), $arguments);
    }

} 