<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeListRepository")
 * @ORM\Table(name="recipe_list")
 */
class RecipeList
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Recipe", cascade={"persist"})
     * @ORM\JoinTable(name="recipe_list_recipe",
     *      joinColumns={@ORM\JoinColumn(name="recipe_list_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recipe_id", referencedColumnName="id")}
     *      )
     */
    public $recipes;

    public function __construct(array $recipes) {
        $this->recipes = new ArrayCollection($recipes);
    }


    /**
     * Get many Users have Many Groups.
     */ 
    public function getRecipes()
    {
        return $this->recipes;
    }
}