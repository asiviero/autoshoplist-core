<?php

namespace App\Tests;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\RecipeList;

class RecipeListRepositoryTest extends DatabaseTest
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
        ], true, $this->kg, 1);

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

    public function testGrouppedRecipeList()
    {
        $recipeList = new RecipeList([
            $this->recipe, 
            $this->recipeComposite,
            $this->recipeComposite
        ]);
        $recipeListRepo = $this->entityManager->getRepository('App\Entity\RecipeList');
        
        // recipe list:
        // 1 tomato sauce + 2 mustard with sauce = 
        // 1 tomato sauce + 2x(1 tomato sauce + 3 mustard) =
        // 3 tomato sauce + 6 mustard =
        // 3x(0.5 tomato + 0.4 salt) + 6 mustard =
        // 1.5 tomato + 1.2 salt + 6 mustard
        $groupped = $recipeListRepo->getFlattenedQuantities($recipeList);
        $this->assertEquals($groupped[$this->tomato->getName()]->getAmount(), 1.5);
        $this->assertEquals($groupped[$this->salt->getName()]->getAmount(), 1.2);
        $this->assertEquals($groupped[$this->mustard->getName()]->getAmount(), 6);
    }

    
}

