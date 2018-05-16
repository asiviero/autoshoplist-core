<?php

namespace App\Repository;

use App\Entity\RecipeList;
use Doctrine\ORM\EntityRepository;
use App\Exception\ConversionImpossibleException;

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
        return array_map(function($quantity) use ($qtyRepo) {
            if($quantity->getIngredient()->getBaseUnit()) {
                try {
                    return $qtyRepo->getQtyInUnit($quantity, $quantity->getIngredient()->getBaseUnit());
                } catch(ConversionImpossibleException $e) {
                    // ignore if no conversion possible
                }
            }
            return $quantity;
        }, $qtyRepo->groupQuantitiesByIngredient($return));
        
    }
}