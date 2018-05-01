<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity()
 * @ORM\Table(name="unit")
 * @SWG\Definition()
 */
class Unit
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SWG\Property()
     */
    public $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @SWG\Property()
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

    public function __toString()
    {
        return sprintf('%s (%s)', $this->name, $this->symbol);
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the value of symbol
     *
     * @param  string  $symbol
     *
     * @return  self
     */ 
    public function setSymbol(string $symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }
}