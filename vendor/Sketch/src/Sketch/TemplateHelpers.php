<?php

namespace Sketch;


use League\Plates\Extension\ExtensionInterface;

class TemplateHelpers implements ExtensionInterface {

    /**
     * @var WpApiWrapper
     */
    protected $wp;

    public function __construct(WpApiWrapper $wp)
    {
        $this->wp = $wp;
    }

    public function getFunctions()
    {
        return array(
          'wp' => 'runWp',
          'ul' => 'runUl',
          'ol' => 'runOl'
        );
    }

    public function runWp($method, $parameters = null)
    {
        if (!$parameters) {
            return $this->wp->$method();
        }

        if (is_array($parameters)) {
            return call_user_func_array(array($this->wp, $method), $parameters);
        }

        return $this->wp->$method($parameters);
    }

    public function runUl(array $list, $keys = false, $seperator = ':')
    {
        $ul = '<ul>';
        if (!$keys) {
            foreach ($list as $item) {
                $ul .= '<li>'.$item.'</li>';
            }
        } else {
            foreach ($list as $k => $v) {
                $ul .= '<li>' . $k . $seperator . ' ' . $v . '</li>';
            }
        }
        $ul .= '</ul>';

        echo $ul;
    }

    public function runOl(array $list, $keys = false, $seperator = ':')
    {
        $ol = '<ol>';
        if (!$keys) {
            foreach ($list as $item) {
                $ol .= '<li>'.$item.'</li>';
            }
        } else {
            foreach ($list as $k => $v) {
                $ol .= '<li>' . $k . $seperator . ' ' . $v . '</li>';
            }
        }
        $ol .= '</ol>';

        echo $ol;
    }

} 