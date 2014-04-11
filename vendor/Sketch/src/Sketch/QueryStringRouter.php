<?php

namespace Sketch;

use Symfony\Component\HttpFoundation\Request;

class QueryStringRouter implements RouterInterface {

    protected $routes = array();
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function __construct(Dispatcher $dispatcher, Request $request)
    {
        $this->dispatcher = $dispatcher;
        $this->request = $request;
    }

    public function register($method, array $params, $controller)
    {
        array_push($this->routes, array(
            'method' => $method,
            'params' => $params,
            'controller' => $controller)
        );
    }

    public function get(array $params, $controller)
    {
        $this->register('GET', $params, $controller);
    }

    public function post(array $params, $controller)
    {
        $this->register('POST', $params, $controller);
    }

    public function any(array $params, $controller)
    {
        $this->register('GET', $params, $controller);
        $this->register('POST', $params, $controller);
    }

    public function resolve()
    {
        $params = $this->request->query->all();
        $method = $this->request->getMethod();

        foreach ($this->routes as $route) {
            if($method === strtoupper($route['method'])) {
                $i = 0;
                foreach($route['params'] as $k => $v) {
                    if (!$this->matches($params, $k, $v)) {
                        break;
                    }
                    $i++;
                    if ($i === count($route['params'])){
                        return $this->dispatch($this->request, $route['controller']);
                    }
                }
            }
        }
        return false;
    }

    protected function dispatch(Request $request, $controller)
    {
        return $this->dispatcher->dispatch($request, $controller);
    }
    
    
    protected function matches($request_params, $route_param_name, $route_param_value)
    {
        if (!isset($request_params[$route_param_name])) return false;

        $request_value = $request_params[$route_param_name];

        if ($route_param_value === '{int}' && ctype_digit($request_value)) return true;

        return $request_value === $route_param_value;
    }

} 