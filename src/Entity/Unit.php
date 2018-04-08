<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="unit")
 */
class Unit
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     */
    public $name;

    /**
     * @ORM\Column(type="string")
     */
    public $symbol;

    public function __construct($name, $symbol)
    {
        $this->name = $name;
        $this->symbol = $symbol;
    }    

    /**
     * Get the value of symbol
     */ 
    public function getSymbol()
    {
        return $this->symbol;
    }
}