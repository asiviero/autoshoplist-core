<?php

namespace App\Entity;

use App\Entity\Ingredient;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 * @ORM\Table(name="recipe")
 */
class Recipe
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
     * @ORM\OneToMany(targetEntity="Quantity", mappedBy="recipe", cascade={"persist"})
     */
    public $quantities;

    /**
     * @ORM\OneToOne(targetEntity="Ingredient", cascade={"persist"})
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     */
    public $ingredient;

    public function __construct($name, $quantities = null, $isIngredient = false, Unit $ingredientUnit = null)
    {
        $this->name = $name;
        $this->quantities = new ArrayCollection($quantities);
        if($isIngredient) {
            $ingredient = new Ingredient($name, $ingredientUnit, $this);
            $this->ingredient = $ingredient;
        }
    }

    /**
     * Get the value of isIngredient
     */ 
    public function isIngredient()
    {
        return $this->ingredient != null;
    }

    public function getIngredient()
    {
        return $this->ingredient;
    }

    /**
     * Get the value of quantities
     */ 
    public function getQuantities()
    {
        return $this->quantities;
    }
}