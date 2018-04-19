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

    /**
     * @ORM\OneToOne(targetEntity="Quantity", cascade={"persist"})
     * @ORM\JoinColumn(name="quantity_id", referencedColumnName="id")
     */
    public $makes;

    public function __construct($name, $quantities = null, $isIngredient = false, Unit $ingredientUnit = null, $makeFactor = null)
    {
        $this->name = $name;
        $this->quantities = new ArrayCollection($quantities);
        foreach($this->quantities as $qty) {
            $qty->setRecipe($this);
        }
        if($isIngredient) {
            $ingredient = new Ingredient($name, $ingredientUnit, $this);
            $this->ingredient = $ingredient;
            $this->makes = new Quantity($makeFactor, $ingredientUnit, $ingredient);
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

    public function getQuantityOf($ingredientName)
    {
        foreach($this->quantities as $qty) {
            if($qty->getIngredient()->getName() == $ingredientName) {
                return $qty;
            }
            return null;
        }
    }

    /**
     * Get the value of makes
     */ 
    public function getMakes()
    {
        return $this->makes;
    }

    public function replaceQuantities($qtyList)
    {
        foreach($this->quantities as $key => $qty) {
            $this->quantities->removeElement($qty);
        }
        $this->quantities = new ArrayCollection($qtyList);
        foreach($this->quantities as $qty) {
            $qty->setRecipe($this);
        }
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }
}