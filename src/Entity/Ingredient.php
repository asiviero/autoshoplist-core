<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ingredient")
 */
class Ingredient
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
     * @var Unit
     * @ORM\ManyToOne(targetEntity="Unit", cascade="merge")
     * @ORM\JoinColumn(name="base_unit_id", referencedColumnName="id")
     */     
    public $baseUnit;

    /**
     * @ORM\OneToOne(targetEntity="Recipe", inversedBy="ingredient")
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     */
    public $recipe;

    public function __construct($name, Unit $baseUnit = null, $recipe = null) {
        $this->name = $name;
        $this->baseUnit = $baseUnit;
        $this->recipe = $recipe;
    }

    public function isRecipe()
    {
        return $this->recipe != null;
    }


    /**
     * Get the value of recipe
     */ 
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of baseUnit
     */ 
    public function getBaseUnit()
    {
        return $this->baseUnit;
    }

    /**
     * Set the value of baseUnit
     *
     * @return  self
     */ 
    public function setBaseUnit(Unit $baseUnit)
    {
        $this->baseUnit = $baseUnit;

        return $this;
    }

}