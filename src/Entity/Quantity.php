<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuantityRepository")
 * @ORM\Table(name="quantity")
 */
class Quantity
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
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     */     
    public $unit;

    /**
     * @ORM\Column(type="decimal")
     */
    public $amount;

    public function __construct($amount, Unit $unit, Ingredient $ingredient) {
        $this->amount = $amount;
        $this->unit = $unit;
        $this->ingredient = $ingredient;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getIngredient()
    {
        return $this->ingredient;
    }
}