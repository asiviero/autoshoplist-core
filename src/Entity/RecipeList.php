<?php

namespace App\Entity;

use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeListRepository")
 * @ORM\Table(name="recipe_list")
 * @SWG\Definition()
 */
class RecipeList
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SWG\Property()
     */
    public $id;

    /**
     * @ORM\OneToMany(targetEntity="RecipeListRecipe", mappedBy="recipeList", cascade={"persist"})
     * @SWG\Property(type="array", items={"$ref":"#/definitions/Recipe"})
     * @Groups({"request"})
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