<?php

namespace App\Exception;

use App\Entity\Unit;
use App\Entity\Ingredient;

class ConversionImpossibleException extends \Exception
{
    public function __construct(Unit $from, Unit $to, Ingredient $ingredient)
    {
        $this->message = sprintf('Could not convert from %s to %s for %s', 
            $from,
            $to,
            $ingredient->getName()
        );
    }
}