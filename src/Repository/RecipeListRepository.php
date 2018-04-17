<?php

namespace App\Repository;

use App\Entity\RecipeList;
use Doctrine\ORM\EntityRepository;

class RecipeListRepository extends EntityRepository
{
    public function getFlattenedQuantities(RecipeList $recipeList)
    {
        $recipeRepo = $this->_em->getRepository('App\Entity\Recipe');
        $qtyRepo = $this->_em->getRepository('App\Entity\Quantity');
        $list = $recipeList->getRecipes()->map(function($recipe) use($recipeRepo) {
            return $recipeRepo->getFlattenedQuantities($recipe);
        });
        $return = [];
        // Flatten the array due to recursiveness
        array_walk_recursive($list, function($item) use(&$return) {
            $return[] = $item;
        });
        return $qtyRepo->groupQuantitiesByIngredient($return);
    }
}