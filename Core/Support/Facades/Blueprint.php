<?php

namespace App\Core\Support\Facades;

class Blueprint
{
    public function id(): static
    {
        echo "id bigint(11) NOT NULL AUTO_INCREMENT,";
        return $this;
    }

    public function primary(): static
    {
        echo "PRIMARY KEY (`id`),";
        return $this;
    }

    public function string($value): static
    {
        echo "VARCHAR(255)";
        return $this;
    }

    public function nullable(): static
    {
        echo "DEFAULT NULL";
        return $this;
    }

}