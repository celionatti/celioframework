<?php

namespace App\Core\Validations;

class RequiredValidation extends Validation
{
    public function runValidation(): bool
    {
        $value = trim($this->_obj->{$this->field});
        return $value != '';
    }
}