<?php

namespace Sketch;

class CurlWrapper {

    public function curl_init($url = null)
    {
        return curl_init($url);
    }

    public function curl_exec($ch)
    {
        return curl_exec($ch);
    }

    public function curl_setopt($ch, $flag, $setting)
    {
        curl_setopt($ch, $flag, $setting);
    }

    public function curl_error($ch)
    {
        return curl_error($ch);
    }
} 