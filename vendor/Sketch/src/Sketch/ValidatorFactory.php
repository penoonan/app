<?php

namespace Sketch;
use Valitron\Validator;

/**
 * Class ValidatorFactory
 */
class ValidatorFactory {

    public function make($data, $fields = array(), $lang = 'en', $langDir = null)
    {
        return new Validator($data, $fields, $lang, $langDir);
    }
} 