<?php

namespace Sketch;

class WpQueryFactory {

  public function make(array $args)
  {
      return new \WP_Query($args);
  }

} 