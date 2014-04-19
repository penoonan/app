<?php

use \Sketch\Taxonomy\BaseTaxonomy;

class HelloTaxonomy extends BaseTaxonomy {

    protected
        $taxonomy = "hello_taxonomy",
        $labels = array(
            'name' => 'Hello Taxonomy'
        );

} 