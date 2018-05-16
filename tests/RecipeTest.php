<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{

    private $recipe;

    public function setUp()
    {
        // Example Recipe: tomato sauce
        $this->salt = new Ingredient('salt');
        $this->tomato = new Ingredient('tomato');
        $this->kg = new Unit('kilogram', 'kg');
        $this->l = new Unit('liter', 'l');
        $this->recipe = new Recipe('tomato sauce', [
            new Quantity(0.5, $this->kg, $this->tomato),
            new Quantity(0.2, $this->kg, $this->salt),
        ], true, $this->l, 0.8);
    }

    /**
     * Test the method fetchQuantityOf in a recipe
     */
    public function testGetQtyOf()
    {
        $qtyTomato = $this->recipe->fetchQuantityOf('tomato');
        $this->assertEquals(0.5, $qtyTomato->getAmount());
        $this->assertEquals('kg', $qtyTomato->getUnit()->getSymbol());
        $qtySalt = $this->recipe->fetchQuantityOf('salt');
        $this->assertEquals(0.2, $qtySalt->getAmount());
        $this->assertEquals('kg', $qtySalt->getUnit()->getSymbol());
        $qtyPotato = $this->recipe->fetchQuantityOf('potato');
        $this->assertNull($qtyPotato);
    }

    /**
     * Test update quantities method
     */
    public function testReplaceQuantities()
    {
        $pinch = new Unit('pinch', 'p');
        $origan = new Ingredient('origan');
        $this->recipe->replaceQuantities([
            new Quantity(0.9, $this->kg, $this->tomato),
            new Quantity(1, $pinch, $origan),
            new Quantity(0.3, $this->kg, $this->salt)
        ]);        
        $this->assertCount(3, $this->recipe->getQuantities());
        $qtyTomato = $this->recipe->fetchQuantityOf('tomato');
        $this->assertEquals(0.9, $qtyTomato->getAmount());
        $this->assertEquals('kg', $qtyTomato->getUnit()->getSymbol());
        $qtySalt = $this->recipe->fetchQuantityOf('salt');
        $this->assertEquals(0.3, $qtySalt->getAmount());
        $this->assertEquals('kg', $qtySalt->getUnit()->getSymbol());
        $qtyOrigan = $this->recipe->fetchQuantityOf('origan');
        $this->assertEquals(1, $qtyOrigan->getAmount());
        $this->assertEquals('p', $qtyOrigan->getUnit()->getSymbol());
    }

    public function testRecipeIsIngredient()
    {
        $this->assertTrue($this->recipe->isIngredient());
        $this->assertEquals(0.8, $this->recipe->getMakes()->getAmount());
        $this->assertEquals('l', $this->recipe->getMakes()->getUnit()->getSymbol());
        $this->assertEquals('tomato sauce', $this->recipe->getIngredient()->getName());
    }
}