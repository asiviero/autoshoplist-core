<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConversionRuleRepository")
 * @ORM\Table(name="conversion_rule")
 */
class ConversionRule
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var Ingredient
     * @ORM\ManyToOne(targetEntity="Ingredient")
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     */     
    public $ingredient;

    /**
     * @var Unit
     * @ORM\ManyToOne(targetEntity="Unit")
     * @ORM\JoinColumn(name="from_unit_id", referencedColumnName="id")
     */     
    public $from;

    /**
     * @var Ingredient
     * @ORM\ManyToOne(targetEntity="Unit")
     * @ORM\JoinColumn(name="to_unit_id", referencedColumnName="id")
     */     
    public $to;

    /**
     * @ORM\Column(type="decimal")
     */
    public $factor;

    public function __construct(Ingredient $ingredient = null, Unit $from, $factor, Unit $to)
    {
        $this->ingredient = $ingredient;
        $this->from = $from;
        $this->to = $to;
        $this->factor = $factor;
    }

    /**
     * Get the value of from
     */ 
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get the value of factor
     */ 
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Get the value of to
     */ 
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the value of factor
     *
     * @return  self
     */ 
    public function setFactor($factor)
    {
        $this->factor = $factor;

        return $this;
    }
}