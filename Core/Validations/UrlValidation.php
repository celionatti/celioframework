<?php

namespace App\Core\Validations;

class UrlValidation extends Validation
{
    public function runValidation()
    {
        $url = $this->_obj->{$this->field};
        $pass = true;
        if (!empty($url)) {
            $pass = filter_var($url, FILTER_VALIDATE_URL);
        }
        return $pass;
    }
}