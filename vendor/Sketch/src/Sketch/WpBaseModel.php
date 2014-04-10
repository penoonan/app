<?php

namespace Sketch;

class WpBaseModel {

    /**
     * @var wpdb
     */
    private $wpdb;
    /**
     * @var WpApiWrapper
     */
    private $wp;

    public function __construct(wpdb $wpdb, WpApiWrapper $wp)
    {
        $this->wpdb = $wpdb;
        $this->wp = $wp;
    }
} 