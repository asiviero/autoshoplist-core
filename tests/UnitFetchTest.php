<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\RecipeList;

class UnitFetchTest extends DatabaseTest
{
    public function setUp()
    {
        parent::setUp();
        $this->un = new Unit('unit', 'un');
        $this->kg = new Unit('kilogram', 'kg');
        
        $this->tomato = new Ingredient('tomato', $this->un);
        $this->mustard = new Ingredient('mustard', $this->un);
        $this->salt = new Ingredient('salt', $this->kg);
        $this->qty = new Quantity(0.5, $this->un, $this->tomato);
        $this->recipe = new Recipe('tomato sauce', [
            $this->qty,
            new Quantity(0.4, $this->kg, $this->salt)        
        ], true, $this->kg);

        $this->recipeComposite = new Recipe('mustard with sauce', [
            new Quantity(1, $this->kg, $this->recipe->getIngredient()),
            new Quantity(3, $this->un, $this->mustard)
        ]);
        
        $this->entityManager->persist($this->un);
        $this->entityManager->persist($this->kg);                
        $this->entityManager->persist($this->tomato);
        $this->entityManager->persist($this->mustard);
        $this->entityManager->persist($this->qty);
        $this->entityManager->persist($this->salt);
        $this->entityManager->persist($this->recipe);
        $this->entityManager->persist($this->recipeComposite);

        $this->entityManager->flush();
    }

    public function testFetchUnits()
    {
        $a=1;
        $recipeRepo = $this->entityManager->getRepository('App\Entity\Recipe');
        $ingredientRepo = $this->entityManager->getRepository('App\Entity\Ingredient');
        $ing = $ingredientRepo->findAll();
        $recipe = $recipeRepo->findOneBy(['name' => 'mustard with sauce']);
        $flattened = $recipeRepo->getFlattenedQuantities($recipe);
        $this->assertCount(3, $flattened);
    }

    public function testGroupByIngredient()
    {
        $qty = new Quantity(0.8, $this->un, $this->tomato);
        $qty2 = new Quantity(0.6, $this->un, $this->tomato);
        $qty3 = new Quantity(0.5, $this->kg, $this->salt);
        $qtyRepo = $this->entityManager->getRepository('App\Entity\Quantity');
        $groupped = $qtyRepo->groupQuantitiesByIngredient([$qty, $qty2, $qty3]);
        $this->assertCount(2, $groupped);
        $this->assertEquals($groupped[$this->tomato->getId()]->getAmount(), 1.4);
    }

    public function testGrouppedRecipeList()
    {
        $recipeList = new RecipeList([$this->recipe, $this->recipe]);
        $recipeListRepo = $this->entityManager->getRepository('App\Entity\RecipeList');
        $groupped = $recipeListRepo->getFlattenedQuantities($recipeList);
        $this->assertEquals($groupped[$this->tomato->getId()]->getAmount(), 1);
        $this->assertEquals($groupped[$this->salt->getId()]->getAmount(), 0.8);
    }
}

