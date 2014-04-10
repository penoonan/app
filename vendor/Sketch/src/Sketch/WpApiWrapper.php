<?php

namespace Sketch;

/**
 * This class exists to provide a wrapper around wordpress functions
 * so that applications built for Wordpress may be easily unit tested.
 * This makes Wordpress itself a mockable dependency.
 *
 * Class WpApiWrapper
 * @package Sketch
 */
class WpApiWrapper {

    public function __call($method, $arguments)
    {
        //call_user_func($method, $arguments);
        return call_user_func_array($method, $arguments);
    }

} 