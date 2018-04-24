<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\ORM\EntityRepository;

class RecipeRepository extends EntityRepository
{
    public function getFlattenedQuantities(Recipe $recipe)
    {
        $qtyRepo = $this->_em->getRepository('App\Entity\Quantity');
        $list = $recipe->getQuantities()->map(function($qty) use($qtyRepo) {
            return  $qtyRepo->getFlattenedQuantity($qty);
        });
        $return = [];
        // Flatten the array due to recursiveness
        array_walk_recursive($list, function($item) use(&$return) {
            $return[] = $item;
        });
        return $qtyRepo->groupQuantitiesByIngredient($return);
    }
}