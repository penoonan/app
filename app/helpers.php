<?php

/**
 * print_r with pre tags and end the script
 */
if (!function_exists('ppd')) {
    function ppd() {
        echo '<pre>';
        array_map(function($x) { print_r($x); }, func_get_args());
        echo '</pre>';
        die;
    }
}

/**
 * var_dump with pre tags and end the script
 */
if (!function_exists('pdd')) {
    function pdd() {
        echo '<pre>';
        array_map(function($x) { var_dump($x); }, func_get_args());
        echo '</pre>';
        die;
    }
}

if (!function_exists('sketchFileName')) {
    function sketchFileName($file_name, $ext = '.php')
    {
        return str_replace($ext, '', $file_name) . $ext;
    }
}

