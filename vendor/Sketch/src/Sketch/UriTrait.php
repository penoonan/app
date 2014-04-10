<?php

namespace Sketch;

trait UriTrait {

    protected function publicUri($file_name = false)
    {
        $path = $this->base_uri;
        if ($file_name) {
            $path .= '/' . sketchFileName($file_name);
        }
        return $path;
    }

    protected function cssUri($file_name = false)
    {
        $path = $this->publicUri() . '/css';
        if ($file_name) {
            $path .= '/' . sketchFileName($file_name, '.css');
        }
        return $path;
    }

    protected function jsUri($file_name = false)
    {
        $path = $this->publicUri() . '/js';
        if ($file_name) {
            $path .= '/' . sketchFileName($file_name, '.js');
        }
        return $path;
    }
} 