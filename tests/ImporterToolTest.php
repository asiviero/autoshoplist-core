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
        $tomato = $this->entityManager->getRepository('App\Entity\Ingredient')->findOneByName('tomato');
        // preferred unit check
        $this->assertEquals($tomato->getBaseUnit()->getSymbol(), 'kg');

        // conversion rules check
        $conversionRepo = $this->entityManager->getRepository('App\Entity\ConversionRule');
        $this->assertCount(5, $conversionRepo->findAll());

        // recipes check 
        $recipeRepo = $this->entityManager->getRepository('App\Entity\Recipe');
        $this->assertCount(3, $recipeRepo->findAll());
        $tomatoSauce = $recipeRepo->findOneByName('tomato sauce');
        $this->assertEquals($tomatoSauce->getCode(), 't1');
        $this->assertCount(3, $tomatoSauce->getQuantities());
        $pastaDough = $recipeRepo->findOneByName('pasta dough');
        $this->assertEquals($pastaDough->getCode(), 't2');
        $this->assertCount(3, $pastaDough->getQuantities());        
        $fetuccine = $recipeRepo->findOneByName('fettucine al pomodoro');
        $this->assertEquals($fetuccine->getCode(), null);
        $this->assertCount(2, $fetuccine->getQuantities());        

    }

    public function testUpdateDatabase()
    {
        $tool = new RecipeDatabaseImporterTool($this->entityManager);
        $tool->import(__DIR__.'/addtestbase.yml');

        // unit check
        $unitRepo = $this->entityManager->getRepository('App\Entity\Unit');
        $this->assertCount(8, $unitRepo->findAll());
        
        // conversion rules check
        $conversionRepo = $this->entityManager->getRepository('App\Entity\ConversionRule');
        $this->assertCount(6, $conversionRepo->findAll());            

        // ingredient check
        $ingredientRepo = $this->entityManager->getRepository('App\Entity\Ingredient');
        $this->assertCount(9, $ingredientRepo->findAll());
        // preferred unit check
        $flour = $this->entityManager->getRepository('App\Entity\Ingredient')->findOneByName('flour');
        $this->assertEquals($flour->getBaseUnit()->getSymbol(), 'kg');
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
            $sauceQty = $sauce->fetchQuantityOf($qty->getIngredient()->getName());
            $doughQty = $dough->fetchQuantityOf($qty->getIngredient()->getName());
            if($sauceQty) {
                $amount = $sauceQty->getAmount() * $factor;
                if($doughQty) {
                    $amount += $doughQty->getAmount();
                }
                $this->assertEquals($amount, $qty->getAmount());
            }
        }
    }
}