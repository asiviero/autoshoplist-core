<?php

namespace App\Repository;

use Dijkstra\Graph;
use App\Entity\Unit;
use App\Entity\Quantity;
use Doctrine\ORM\EntityRepository;

class QuantityRepository extends EntityRepository
{
    public function getQtyInUnit(Quantity $qty, Unit $unit)
    {
        if($qty->getUnit() == $unit) {
            return $qty;
        }
        // Check for rule 
        $qb = $this->getEntityManager()->createQueryBuilder();
        $repoConversion = $this->getEntityManager()->getRepository('App\Entity\ConversionRule');
        $rules = $repoConversion->getRulesForIngredient($qty->getIngredient());
        $graph = new Graph();
        foreach($rules as $rule) {
            $graph->addedge($rule->from->getSymbol(), $rule->to->getSymbol(), $rule->getFactor());
            $graph->addedge($rule->to->getSymbol(), $rule->from->getSymbol(), 1/$rule->getFactor());
        }
        list($path, $weight) = $graph->getpath($qty->getUnit()->getSymbol(), $unit->getSymbol());
        return new Quantity(array_product($weight), $unit, $qty->getIngredient());
    }

    public function sum(Quantity $a, Quantity $b)
    {
        $b = $this->getQtyInUnit($b, $a->getUnit());
        return new Quantity($a->getAmount() + $b->getAmount(), $a->getUnit(), $a->getIngredient());
    }

    public function getFlattenedQuantity(Quantity $qty)
    {
        $ingredient = $qty->getIngredient();
        if($ingredient->isRecipe()) {
            $list = $ingredient->getRecipe()->getQuantities();
            $qtyList = [];
            foreach($list as $sqty) {
                $qtyList[] = $this->getFlattenedQuantity($sqty);
            }
            return $qtyList;            
        } else {
            return $qty;
        }                                
    }

    public function groupQuantitiesByIngredient($qtyList)
    {
        $return = [];
        array_walk($qtyList, function($item) use(&$return) {
            if(isset($return[$item->getIngredient()->getId()])) {
                $return[$item->getIngredient()->getId()] = $this->sum(
                    $return[$item->getIngredient()->getId()],
                    $item
                );
            } else {
                $return[$item->getIngredient()->getId()] = $item;
            }
        });
        return $return;
    }
}