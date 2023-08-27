<?php

namespace App\Core\Validations;

class MaxValidation extends Validation
{
    public function runValidation(): bool
    {
        $value = $this->_obj->{$this->field};
        return strlen($value) <= $this->rule;
    }
}