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
     * @ORM\ManyToOne(targetEntity="Unit")
     * @ORM\JoinColumn(name="base_unit_id", referencedColumnName="id")
     */     
    public $baseUnit;

    public function __construct($name, $baseUnit) {
        $this->name = $name;
        $this->baseUnit = $baseUnit;
    }

}