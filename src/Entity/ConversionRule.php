<?php

namespace App\Entity;

use App\Entity\Ingredient;
use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConversionRuleRepository")
 * @ORM\Table(name="conversion_rule")
 * @SWG\Definition()
 */
class ConversionRule
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SWG\Property()
     */
    public $id;

    /**
     * @var Ingredient
     * @ORM\ManyToOne(targetEntity="Ingredient", cascade="merge")
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     * @SWG\Property()
     * @Groups({"request"})
     */     
    public $ingredient;

    /**
     * @var Unit
     * @ORM\ManyToOne(targetEntity="Unit", cascade="merge")
     * @ORM\JoinColumn(name="from_unit_id", referencedColumnName="id")
     * @SWG\Property()
     * @Groups({"request"})
     */     
    public $from;

    /**
     * @var Unit
     * @ORM\ManyToOne(targetEntity="Unit", cascade="merge")
     * @ORM\JoinColumn(name="to_unit_id", referencedColumnName="id")
     * @SWG\Property()
     * @Groups({"request"})
     */     
    public $to;

    /**
     * @ORM\Column(type="float")
     * @SWG\Property()
     * @Groups({"request"})
     */
    public $factor;

    public function __construct(Unit $from, $factor, Unit $to, Ingredient $ingredient = null)
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

    public function getIngredient()
    {
        return $this->ingredient;
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

    

    /**
     * Set the value of ingredient
     *
     * @return  self
     */ 
    public function setIngredient(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;

        return $this;
    }
}