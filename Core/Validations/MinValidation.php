<?php

namespace App\Core\Validations;

class MinValidation extends Validation
{
    public function runValidation(): bool
    {
        $value = $this->_obj->{$this->field};
        return strlen($value) >= $this->rule;
    }
}