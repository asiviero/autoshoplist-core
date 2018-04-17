<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ConversionRuleRepository extends EntityRepository
{
    public function getRulesForIngredient($ingredient)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $result = $qb->select('c')
            ->from('App:ConversionRule', 'c')
            ->where('c.ingredient = :ingredient or c.ingredient is null')
            ->setParameter(':ingredient', $ingredient)
            ->getQuery()->getResult();
        return $result;
    }
}