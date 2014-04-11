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

    /**
     * @var WpQueryFactory
     */
    protected $wp_query;

    public function __construct(WpDbWrapper $wpdb, WpApiWrapper $wp, WpQueryFactory $wp_query)
    {
        $this->wpdb = $wpdb;
        $this->wp = $wp;
        $this->wp_query = $wp_query;
    }
}