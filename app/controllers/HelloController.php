<?php

use Post;

class HelloController extends \Sketch\WpBaseController {

    /**
     * @var Post
     */
    protected $post_model;

    public function __construct(Post $post_model)
    {
        $this->post_model = $post_model;
    }


    public function index()
    {
        $data = array(
          'page' => $this->request->query->get('page'),
          'posts' => $this->post_model->all()
        );

        $this->render('hello::hello', $data);
    }

    public function submenu()
    {
        $this->render('hello::submenu');
    }
}