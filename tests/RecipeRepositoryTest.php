<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;

class RecipeRepositoryTest extends DatabaseTest
{
    public function setUp()
    {
        parent::setUp();
        $this->repo = $this->entityManager->getRepository('App\Entity\Recipe');
        $this->kg = new Unit('kilogram', 'kg');
        $this->tomato = new Ingredient('tomato');
        $this->cheese = new Ingredient('cheese');
        $this->meat = new Ingredient('meat');
        $this->salt = new Ingredient('salt');
        $this->tomatoSauce = new Recipe('tomato sauce', [
            new Quantity(0.5, $this->kg, $this->tomato),
            new Quantity(0.2, $this->kg, $this->salt),
        ], true, $this->kg, 0.8);
        $this->meatBroth = new Recipe('meat broth', [
            new Quantity(1.6, $this->kg, $this->tomatoSauce->getIngredient()),
            new Quantity(0.2, $this->kg, $this->salt),
        ], true, $this->kg, 1);
        $this->megaRecipe = new Recipe('meat broth', [
            new Quantity(2, $this->kg, $this->meatBroth->getIngredient()),
            new Quantity(0.5, $this->kg, $this->cheese),
        ]);
    }

    public function testFlattenRecipe()
    {
        // tomato sauce features:
        // 0.5 tomato + 0.2 salt
        $flattened = $this->repo->getFlattenedQuantities($this->tomatoSauce);
        $this->assertEquals($flattened[$this->tomato->getName()]->getAmount(), 0.5);
        $this->assertEquals($flattened[$this->salt->getName()]->getAmount(), 0.2);

        // meat broth features:
        // (1.8/0.6) x tomatoSauce + 0.2 salt =
        // 2x(0.5 tomato + 0.2 salt) + 0.2 salt =
        // 1 tomato + 0.6 salt
        $flattened = $this->repo->getFlattenedQuantities($this->meatBroth);
        $this->assertEquals($flattened[$this->tomato->getName()]->getAmount(), 1);
        $this->assertEquals($flattened[$this->salt->getName()]->getAmount(), 0.6);

        // mega recipe features:
        // 0.5 cheese + 2 x (meat broth) =
        // 0.5 cheese + 2 x (1 tomato + 0.6 salt) =
        // 0.5 cheese + 1.2 salt + 2 tomato
        $flattened = $this->repo->getFlattenedQuantities($this->megaRecipe);
        $this->assertEquals($flattened[$this->tomato->getName()]->getAmount(), 2);
        $this->assertEquals($flattened[$this->salt->getName()]->getAmount(), 1.2);
        $this->assertEquals($flattened[$this->cheese->getName()]->getAmount(), 0.5);

    }
}