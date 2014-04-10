<?php

class HelloController extends \Sketch\WpBaseController {

    public function index()
    {
        $data = array(
          'page' => $this->request->query->get('page')
        );

        $this->render('hello::hello', $data);
    }

    public function submenu()
    {
        $this->render('hello::submenu');
    }
}