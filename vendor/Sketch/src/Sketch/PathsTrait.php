<?php

namespace Sketch;

trait PathsTrait {
    protected function basePath($file_name = false)
    {
        $path = $this->base_dir;
        if ($file_name) {
            $path .= '/' . arcFileName($file_name);
        }
        return $path;
    }

    protected function appPath($file_name = false)
    {
        $path = $this->basePath() . '/app';
        if($file_name) {
            $path .= '/' . arcFileName($file_name);
        }
        return $path;
    }

    protected function viewPath($file_name = false)
    {
        $path = $this->appPath() . '/views';
        if ($file_name) {
            $path .= '/' . arcFileName($file_name);
        }
        return $path;
    }

    protected function publicPath($file_name = false)
    {
        $path = $this->appPath() . '/public';
        if ($file_name) {
            $path .= '/' . arcFileName($file_name);
        }
        return $path;
    }
} 