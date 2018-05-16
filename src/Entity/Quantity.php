<?php

namespace App\Entity;

use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Annotation\Groups;
//* @SWG\Definition(properties={"id","ingredient","unit","amount"})

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
     * @SWG\Property(ref=@Model(type=Ingredient::class))
     */     
    public $ingredient;

    /**
     * @var Unit
     * @ORM\ManyToOne(targetEntity="Unit", cascade="merge")
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id")
     * @SWG\Property(ref=@Model(type=Unit::class)) 
     */     
    public $unit;

    /**
     * @ORM\Column(type="float")
     */
    public $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="quantities")
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     * @SWG\Property(readOnly=true, ref=@Model(type=Recipe::class))
     */
    private $recipe;

    public function __construct($amount, Unit $unit, Ingredient $ingredient) {
        $this->amount = $amount;
        $this->unit = $unit;
        $this->ingredient = $ingredient;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    /*public function getUnit()
    {
        return $this->unit;
    }*/

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

    /**
     * Get the value of unit
     */ 
    public function getUnit()
    {
        return $this->unit;
    }
}