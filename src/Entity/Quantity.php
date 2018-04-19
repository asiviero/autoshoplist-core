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
     * @ORM\Column(type="float")
     */
    public $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="quantities")
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     */
    public $recipe;

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

    /**
     * Set the value of amount
     *
     * @return  self
     */ 
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the value of recipe
     *
     * @return  self
     */ 
    public function setRecipe($recipe)
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function __toString()
    {
        return sprintf('%s %s %s',
            number_format($this->amount, 2),
            $this->unit->getSymbol(),
            $this->ingredient->getName()
        );
    }
}