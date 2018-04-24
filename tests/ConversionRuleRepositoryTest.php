<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\ConversionRule;

class ConversionRuleRepositoryTest extends DatabaseTest
{
    public function setUp()
    {
        parent::setUp();
        $this->un = new Unit('unit', 'un');
        $this->cup = new Unit('cup', 'cup');
        $this->kg = new Unit('kilogram', 'kg');
        $this->g = new Unit('gram', 'g');
        
        $this->tomato = new Ingredient('tomato', $this->un);
        $this->potato = new Ingredient('potato', $this->un);

        $conversionGlobal = new ConversionRule($this->kg, 1000, $this->g);
        $conversionTomato = new ConversionRule($this->un, 0.4, $this->kg, $this->tomato);
        $conversionTomato2 = new ConversionRule($this->cup, 0.8, $this->un, $this->tomato);
        $this->entityManager->persist($this->un);
        $this->entityManager->persist($this->cup);
        $this->entityManager->persist($this->kg);        
        $this->entityManager->persist($this->g);        
        $this->entityManager->persist($this->tomato);
        $this->entityManager->persist($this->potato);
        $this->entityManager->persist($conversionTomato);
        $this->entityManager->persist($conversionTomato2);
        $this->entityManager->persist($conversionGlobal);
        $this->entityManager->flush();
    }

    public function testRetrieveConversionTableByIngredient()
    {
        $repo = $this->entityManager->getRepository('App\Entity\ConversionRule');
        $rules = $repo->getRulesForIngredient($this->tomato);
        $this->assertCount(3, $rules);
        $rules = $repo->getRulesForIngredient($this->potato);
        $this->assertCount(1, $rules);
    }

}
