<?php

namespace App\Entity;

use App\Entity\Quantity;
use App\Entity\Ingredient;
use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 * @ORM\Table(name="recipe")
 * @SWG\Definition()
 */
class Recipe
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SWG\Property()
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     * @SWG\Property()
     * @Groups({"request"})
     */
    public $name;

    /**
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @SWG\Property()
     * @Groups({"request"})
     */
    public $code;

    /**
     * @var Quantity[]
     * @ORM\OneToMany(targetEntity="Quantity", mappedBy="recipe", cascade={"persist", "merge"})
     * @SWG\Property(type="array", items={"$ref":"#/definitions/Quantity"})
     * @Groups({"request"})
     */
    public $quantities;

    /**
     * @var Ingredient
     * @ORM\OneToOne(targetEntity="Ingredient", cascade={"persist"}, mappedBy="recipe")
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     * @SWG\Property()
     */
    public $ingredient;

    /**
     * @var Quantity
     * @ORM\OneToOne(targetEntity="Quantity", cascade={"persist"})
     * @ORM\JoinColumn(name="quantity_id", referencedColumnName="id")
     * @SWG\Property()
     * @Groups({"request"})
     */
    public $makes;

    public function __construct($name, $quantities = null, $isIngredient = false, Unit $ingredientUnit = null, $makeFactor = 1)
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

    public function getId()
    {
        return $this->id;
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
     * @return Quantity[]
     * Get the value of quantities
     */ 
    public function getQuantities()
    {
        return $this->quantities;
    }

    public function addQuantities($qty)
    {
        $qty->setRecipe($this);
        $this->quantities->add($qty);
    }
    
    public function fetchQuantityOf($ingredientName)
    {
        foreach($this->quantities as $qty) {
            if($qty->getIngredient()->getName() == $ingredientName) {
                return $qty;
            }
        }
        return null;
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

    /**
     * Set the value of id
     */ 
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value of name
     */ 
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * Get the value of code
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */ 
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}