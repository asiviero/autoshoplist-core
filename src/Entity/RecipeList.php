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
     * @ORM\OneToMany(targetEntity="RecipeListRecipe", mappedBy="recipeList", cascade={"persist"})
     */
    public $recipes;

    public function __construct(array $recipes) {
        $this->setRecipes($recipes);
    }


    /**
     * Get many Users have Many Groups.
     */ 
    public function getRecipes()
    {
        return $this->recipes->map(function($item) {
            return $item->getRecipe();
        });        
    }

    public function setRecipes(array $recipes)
    {
        $recipes = array_map(function($recipe) {
            return new RecipeListRecipe($recipe, $this);
        }, $recipes);
        $this->recipes = new ArrayCollection($recipes);
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }
}