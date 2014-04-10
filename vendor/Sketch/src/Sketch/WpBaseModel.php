<?php

namespace Sketch;

class WpBaseModel {

    /**
     * @var WpDbWrapper
     */
    protected $wpdb;
    /**
     * @var WpApiWrapper
     */
    protected $wp;

    public function __construct(WpDbWrapper $wpdb, WpApiWrapper $wp)
    {
        $this->wpdb = $wpdb;
        $this->wp = $wp;
    }
}