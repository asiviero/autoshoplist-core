<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\ConversionRule;

class ConversionTableTest extends DatabaseTest
{
    public function setUp()
    {
        parent::setUp();
        $this->un = new Unit('unit', 'un');
        $this->kg = new Unit('kilogram', 'kg');
        $this->tomato = new Ingredient('tomato', $this->un);
        $this->potato = new Ingredient('potato', $this->un);
        $conversionTomato = new ConversionRule($this->tomato, 0.4, $this->kg, $this->un);
        $conversionPotato = new ConversionRule($this->potato, 0.6, $this->kg, $this->un);
        $this->entityManager->persist($this->un);
        $this->entityManager->persist($this->kg);        
        $this->entityManager->persist($this->tomato);
        $this->entityManager->persist($this->potato);
        $this->entityManager->persist($conversionTomato);
        $this->entityManager->persist($conversionPotato);
        $this->entityManager->flush();
    }

    public function testRetrieveConversionTableByIngredient()
    {
        $repo = $this->entityManager->getRepository('App\Entity\ConversionRule');
        $rules = $repo->getRulesForIngredient($this->tomato);
        $this->assertCount(1, $rules);
    }

    public function testRetrieveInUnit()
    {
        $repo = $this->entityManager->getRepository('App\Entity\Quantity');
        $qty = new Quantity(1, $this->kg, $this->tomato);
        $qtyFrom = new Quantity(1, $this->un, $this->tomato);
        // Check conversion when rule is on the "to" side
        $this->assertEquals($repo->getQtyInUnit($qty, $this->un)->getAmount(), 2.5);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->un)->getUnit(), $this->un);
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->kg)->getAmount(), 0.4);
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->kg)->getUnit(), $this->kg);
        // Unit unchanged means qty unchanged
        $this->assertEquals($repo->getQtyInUnit($qty, $this->kg)->getAmount(), 1);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->kg)->getUnit(), $this->kg);        
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->un)->getAmount(), 1);
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->un)->getUnit(), $this->un);
    }

    public function testSum()
    {
        $repo = $this->entityManager->getRepository('App\Entity\Quantity');
        $qty = new Quantity(1, $this->kg, $this->tomato);
        $qtyFrom = new Quantity(1, $this->un, $this->tomato);
        
        $this->assertEquals($repo->sum($qty, $qtyFrom)->getAmount(), 1.4);
        $this->assertEquals($repo->sum($qty, $qtyFrom)->getUnit(), $qty->getUnit());
        $this->assertEquals($repo->sum($qty, $qty)->getAmount(), 2);
        $this->assertEquals($repo->sum($qty, $qty)->getUnit(), $qty->getUnit());
        $this->assertEquals($repo->sum($qtyFrom, $qty)->getAmount(), 3.5);        
        $this->assertEquals($repo->sum($qtyFrom, $qty)->getUnit(), $qtyFrom->getUnit());
        $this->assertEquals($repo->sum($qtyFrom, $qtyFrom)->getAmount(), 2);
        $this->assertEquals($repo->sum($qtyFrom, $qtyFrom)->getUnit(), $qtyFrom->getUnit());
    }
}
