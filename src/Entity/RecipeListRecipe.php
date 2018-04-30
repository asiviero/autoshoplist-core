<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="recipe_list_recipe")
 */
class RecipeListRecipe
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Recipe", cascade={"persist"})
     * @ORM\JoinColumn(name="recipe_id", referencedColumnName="id")
     */
    public $recipe;

    /**
     * @ORM\ManyToOne(targetEntity="RecipeList", inversedBy="recipes", cascade={"persist"})
     * @ORM\JoinColumn(name="recipe_list_id", referencedColumnName="id")
     */
    public $recipeList;

    public function __construct($recipe, $recipeList) {
        $this->recipe = $recipe;
        $this->recipeList = $recipeList;
    }

    /**
     * Get the value of recipe
     */ 
    public function getRecipe()
    {
        return $this->recipe;
    }
}