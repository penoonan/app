<?php

namespace Sketch;

interface RouterInterface {

    public function resolve();
    public function post(array $params, $controller);
    public function get(array $params, $controller);
    public function register($method, array $params, $controller);
} 