<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\ORM\EntityRepository;

class RecipeRepository extends EntityRepository
{
    public function getFlattenedQuantities(Recipe $recipe)
    {
        $qtyRepo = $this->_em->getRepository('App\Entity\Quantity');
        $list = $recipe->quantities->map(function($qty) use($qtyRepo) {
            $flattened = $qtyRepo->getFlattenedQuantity($qty);
            $return = [];
            if($qty->getIngredient()->isRecipe()) {
                $qtyInIngrUnit = $qtyRepo->getQtyInUnit(
                    $qty, 
                    $qty->getIngredient()->getRecipe()->getMakes()->getUnit()
                );
                $factor = $qtyInIngrUnit->getAmount() / $qty->getIngredient()->getRecipe()->getMakes()->getAmount();
                array_walk_recursive($flattened, function($item) use(&$return, $qtyRepo, $factor) {
                    $item = clone $item;
                    $item->setAmount($item->getAmount() * $factor);
                    $return[] = $item;
                });
            } else {
                $return = $flattened;
            }
            return $return;
        });
        $return = [];
        // Flatten the array due to recursiveness
        array_walk_recursive($list, function($item) use(&$return) {
            $return[] = $item;
        });
        return $qtyRepo->groupQuantitiesByIngredient($return);
    }
}