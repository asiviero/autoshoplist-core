<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\ConversionRule;
use App\Exception\ConversionImpossibleException;

class QuantityRepositoryTest extends DatabaseTest
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
        $conversionPotato = new ConversionRule($this->un, 0.6, $this->kg, $this->potato);
        $this->entityManager->persist($this->un);
        $this->entityManager->persist($this->cup);
        $this->entityManager->persist($this->kg);        
        $this->entityManager->persist($this->g);        
        $this->entityManager->persist($this->tomato);
        $this->entityManager->persist($this->potato);
        $this->entityManager->persist($conversionTomato);
        $this->entityManager->persist($conversionTomato2);
        $this->entityManager->persist($conversionPotato);
        $this->entityManager->persist($conversionGlobal);
        $this->entityManager->flush();
    }

    public function testGlobalConversionRules()
    {
        $repo = $this->entityManager->getRepository('App\Entity\Quantity');
        $qty = new Quantity(1, $this->kg, $this->tomato);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->g)->getAmount(), 1000);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->g)->getUnit(), $this->g);        
    }

    public function testRetrieveInUnit()
    {
        $repo = $this->entityManager->getRepository('App\Entity\Quantity');
        $qty = new Quantity(1, $this->kg, $this->tomato);
        $qtyFrom = new Quantity(1, $this->un, $this->tomato);
        // Check conversion when rule is on the "to" side
        $this->assertEquals($repo->getQtyInUnit($qty, $this->un)->getAmount(), 2.5);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->un)->getUnit(), $this->un);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->cup)->getAmount(), 3.125);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->cup)->getUnit(), $this->cup);
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->kg)->getAmount(), 0.4);
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->kg)->getUnit(), $this->kg);
        // Unit unchanged means qty unchanged
        $this->assertEquals($repo->getQtyInUnit($qty, $this->kg)->getAmount(), 1);
        $this->assertEquals($repo->getQtyInUnit($qty, $this->kg)->getUnit(), $this->kg);        
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->un)->getAmount(), 1);
        $this->assertEquals($repo->getQtyInUnit($qtyFrom, $this->un)->getUnit(), $this->un);
    }

    public function testRetrieveInUnitFailsWhenNoPathPossible()
    {
        $this->expectException(ConversionImpossibleException::class);
        $repo = $this->entityManager->getRepository('App\Entity\Quantity');
        $qty = new Quantity(1, $this->kg, $this->potato);
        $repo->getQtyInUnit($qty, $this->cup);
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

    public function testGroupByIngredient()
    {
        $qty = new Quantity(0.8, $this->un, $this->tomato);
        $qty2 = new Quantity(0.6, $this->un, $this->tomato);
        $qty3 = new Quantity(0.5, $this->kg, $this->potato);
        $qtyRepo = $this->entityManager->getRepository('App\Entity\Quantity');
        $groupped = $qtyRepo->groupQuantitiesByIngredient([$qty, $qty2, $qty3]);
        $this->assertCount(2, $groupped);
        $this->assertEquals($groupped[$this->tomato->getName()]->getAmount(), 1.4);
    }

    public function testGetFlattened()
    {
        $repo = $this->entityManager->getRepository('App\Entity\Quantity');
        // Simple test, qty is not a recipe
        $demikgTomato = new Quantity(0.5, $this->kg, $this->tomato);
        $flattened = $repo->getFlattenedQuantity($demikgTomato);
        $this->assertEquals($flattened->getAmount(), $demikgTomato->getAmount());
        unset($flattened);
        // Test recipe flattening
        $salt = new Ingredient('salt');        
        $tomatoSauce = new Recipe('tomato sauce', [
            $demikgTomato,
            new Quantity(0.2, $this->kg, $salt),
        ], true, $this->cup, 0.8);
        // Double the make, double the values
        $qtySauce = new Quantity(1.6, $this->cup, $tomatoSauce->getIngredient());
        $flattened = $repo->getFlattenedQuantity($qtySauce);
        $this->assertCount(2, $flattened);
        $expectedAmountList = [0.4, 1];
        $actualAmountList = array_map(function($item) {
            return $item->getAmount();
        }, $flattened);
        sort($actualAmountList);
        $this->assertEquals($expectedAmountList, $actualAmountList);
        unset($flattened);

        // Test a more complex recipe, with 2 levels of recipe
        $broth = new Recipe('broth', [
            $demikgTomato,
            $qtySauce
        ], true, $this->cup, 1);
        $brothQty = new Quantity(2, $this->cup, $broth->getIngredient());
        $flattened = $repo->getFlattenedQuantity($brothQty);
        $this->assertCount(3, $flattened);
        $expectedAmountList = [0.8, 1, 2];
        $actualAmountList = array_map(function($item) {
            return $item->getAmount();
        }, $flattened);
        sort($actualAmountList);
        $this->assertEquals($expectedAmountList, $actualAmountList);
        $groupped = $repo->groupQuantitiesByIngredient($flattened);
        $expectedAmountList = [0.8, 3];
        $actualAmountList = array_map(function($item) {
            return $item->getAmount();
        }, $groupped);
        sort($actualAmountList);
        $this->assertEquals($expectedAmountList, array_values($actualAmountList));
    }
}
