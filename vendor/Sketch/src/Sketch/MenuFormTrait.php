<?php

namespace Sketch;

trait MenuFormTrait {

    /**
     * Checks to make sure input is secure, then validates it
     */
    protected function menuFormInputGood($input)
    {
        if($this->inputIsSecure($input)) {
            return $this->validate($input);
        }
        return false;
    }

    /**
     * Verifies the nonce
     * @param $input
     * @return bool
     */
    protected function inputIsSecure($input)
    {
        $secure = isset($input[$this->nonce_name]) && $this->form->verifyNonce($input[$this->nonce_name], $this->nonce_action);
        if (!$secure && isset($input[$this->nonce_name])) {
            $this->addError('Form did not pass security check');
        }
        return $secure;
    }

    /**
     * A standard validator implementation
     * @param array $data
     * @return bool
     */
    protected function validate(array $data)
    {
        if (!$this->form->with($data)->isValid())  {
            $this->addErrors($this->form->errors());
            return false;
        }
        return true;
    }

} 