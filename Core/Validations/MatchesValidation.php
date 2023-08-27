<?php

namespace App\Core\Validations;

class MatchesValidation extends Validation
{
    public function runValidation(): bool
    {
        $value = $this->_obj->{$this->field};
        return $value == $this->rule;
    }
}