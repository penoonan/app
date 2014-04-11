<?php

namespace Sketch;

use Illuminate\Container\Container;
use League\Plates\Template;
use Symfony\Component\HttpFoundation\Request;

class Dispatcher {

    /**
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * @var \League\Plates\Template
     */
    private $template;

    /**
     * If you choose to namespace your controllers,
     * pass that namespace in as a parameter to this class
     * in app.php
     * @var string
     */
    private $controller_namespace;

    protected $args = array();

    /**
     * @param Container $app
     * @param Template $template
     * @param null $controller_namespace
     */
    public function __construct(Container $app, Template $template, $controller_namespace = null)
    {
        if ($controller_namespace) {
            $this->controller_namespace = trim($controller_namespace, '\\');
        }
        $this->app = $app;
        $this->template = $template;
    }

    public function dispatch(Request $request, $controller)
    {
        list($class, $method) = explode('@', $controller);
        $class = ucwords($class) . 'Controller';
        return call_user_func(array($this->controller($class, $request), $method));
    }

    private function controller($class, $request)
    {
        $controller_class = $this->app->make($this->controller_namespace . '\\' . $class);
        $controller_class->setTemplate($this->template);
        $controller_class->setRequest($request);
        return $controller_class;
    }

}