<?php

namespace App\Core\Validations;

class StringValidation extends Validation
{
    public function runValidation(): bool|int
    {
        $value = $this->_obj->{ $this->field};
        $pass = true;
        if (!empty($value)) {
            $pass = preg_match("/^[a-zA-Z]+$/", $value);
        }
        return $pass;
    }
}