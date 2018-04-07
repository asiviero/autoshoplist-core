<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Quantity;
use App\Entity\Unit;

class QuantityRepository extends EntityRepository
{
    public function getQtyInUnit(Quantity $qty, Unit $unit)
    {
        if($qty->getUnit() == $unit) {
            return $qty;
        }
        // Check for rule 
        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb->select('c')
            ->from('App:ConversionRule', 'c')
            ->where('c.ingredient = :ingredient')
            ->setParameter(':ingredient', $qty->getIngredient())
            ->andWhere(
                $qb->expr()->orX(
                    'c.from = :from and c.to = :to',
                    'c.from = :to and c.to = :from'
                )
            )
            ->setParameter(':from', $qty->getUnit())
            ->setParameter(':to', $unit)
            ->getQuery()->getResult();
        $rule = reset($result);
        $factor = $rule->getFactor();
        if($rule->getTo() == $unit) {
            $factor = 1/$factor;
        }
        $amount = $factor * $qty->getAmount();
        return new Quantity($amount, $unit, $qty->getIngredient());
    }
}