<?php

namespace App\Core\Validations;

class NumericValidation extends Validation
{
    public function runValidation(): bool
    {
        $value = $this->_obj->{$this->field};
        $pass = true;
        if (!empty($value)) {
            $pass = is_numeric($value);
        }
        return $pass;
    }
}