<?php

namespace App\Tests;

use App\Tool\RecipeDatabaseImporterTool;


class ImporterToolTest extends DatabaseTest
{
    public function setUp()
    {
        parent::setUp();
        $tool = new RecipeDatabaseImporterTool($this->entityManager);
        $tool->import(__DIR__.'/testbase.yml');
    }

    public function testSetupDatabase()
    {
        // unit check
        $unitRepo = $this->entityManager->getRepository('App\Entity\Unit');
        $this->assertCount(7, $unitRepo->findAll());

        // ingredient check
        $ingredientRepo = $this->entityManager->getRepository('App\Entity\Ingredient');
        $this->assertCount(8, $ingredientRepo->findAll());

        // conversion rules check
        $conversionRepo = $this->entityManager->getRepository('App\Entity\ConversionRule');
        $this->assertCount(5, $conversionRepo->findAll());

        // recipes check 
        $recipeRepo = $this->entityManager->getRepository('App\Entity\Recipe');
        $this->assertCount(3, $recipeRepo->findAll());
        $tomatoSauce = $recipeRepo->findOneByName('tomato sauce');
        $this->assertCount(3, $tomatoSauce->getQuantities());
        $pastaDough = $recipeRepo->findOneByName('pasta dough');
        $this->assertCount(3, $pastaDough->getQuantities());
        $fetuccine = $recipeRepo->findOneByName('fettucine al pomodoro');
        $this->assertCount(2, $fetuccine->getQuantities());        

    }

    public function testFlattenComposite()
    {
        $recipeRepo = $this->entityManager->getRepository('App\Entity\Recipe');
        $recipe = $recipeRepo->findOneByName('fettucine al pomodoro');
        $sauce = $recipeRepo->findOneByName('tomato sauce');
        $dough = $recipeRepo->findOneByName('pasta dough');
        $flattened = $recipeRepo->getFlattenedQuantities($recipe);
        $factor = 5/3;
        foreach($flattened as $qty) {
            $sauceQty = $sauce->getQuantityOf($qty->getIngredient()->getName());
            $doughQty = $dough->getQuantityOf($qty->getIngredient()->getName());
            if($sauceQty) {
                $this->assertEquals($sauceQty->getAmount() * $factor, $qty->getAmount());
            }
        }
        $b=1;
    }
}