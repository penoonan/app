<?php

namespace Sketch;

use League\Plates\Template;
use Symfony\Component\HttpFoundation\Request;

class WpControllerException extends \Exception {}

class WpBaseController {

    /**
     * @var \League\Plates\Template
     */
    protected $template;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    protected $nonce_name = 'sketch_controller_nonce_name';
    protected $nonce_action = 'sketch_controller_nonce_action';
    protected $nonce_referrer = true;
    protected $errors = array();
    protected $message = null;
    protected $page_vars = array();

    public function setTemplate(Template $template)
    {
        $this->template = $template;
        return $this;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Gets all variables needed for displaying the manager view
     * @return array
     */
    protected function getPageVars()
    {
        $vars = array(
            'message' => $this->message,
            'nonce_name' => $this->nonce_name,
            'nonce_action' => $this->nonce_action,
            'errors' => $this->errors
        );

        return array_merge($vars, $this->page_vars);
    }

    private function addPageVars(array $vars)
    {
        foreach ($vars as $k => $v) {
            $this->addPageVar([$k => $v]);
        }
        return $this->page_vars;
    }

    private function addPageVar(array $var)
    {
        $this->page_vars = array_replace($this->page_vars, $var);
        return $this->page_vars;
    }

    protected function giveToView(array $data)
    {
        if (isset($data[0]) && is_array($data[0])) {
            $this->addPageVars($data);
        } else {
            $this->addPageVar($data);
        }
    }

    protected function addErrors(array $errors)
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    protected function addError($error)
    {
        array_push($this->errors, $error);
    }

    /**
     * Render the template with variables provided
     * @param $name
     * @param array $data
     */
    protected function render($name, array $data = array())
    {
        echo $this->template->render($name, array_merge($this->getPageVars(), $data));
    }

    public function __call($method, $parameters)
    {
        throw new ControllerException('Method ' . $method . ' does not exist in class ' . get_class());
    }

    protected function addToErrorsIfException($potential_exception)
    {
        foreach (func_get_args() as $arg) {
            if (is_a($arg, 'Exception')) {
                $this->addError(get_class($arg). ': ' . $arg->getMessage());
            }
        }
    }

} 