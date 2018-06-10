<?php

use App\Kernel;
use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\RecipeList;
use App\Entity\ConversionRule;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\Tools\SchemaTool;

class FeatureContext implements Context
{
    /** \App\Kernel $kernel */
    private static $kernel;

    /**
     * return \App\Kernel
     */
    private function getKernel()
    {
        return self::$kernel;
    }

    public function __construct($kernel)
    {
        self::$kernel = $kernel;
    }

    /** @BeforeScenario */
    public function beforeScenario()
    {
        $appKernel = $this->getKernel();
        $appKernel->reboot($appKernel->getCacheDir());
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($em);
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());    
    }

    /**
     * @Given there are Unit with:
     */
    public function thereAreWith(TableNode $table)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        foreach($table->getHash() as $hash) {
            $unit = new Unit($hash['name'], $hash['symbol']);
            $em->persist($unit);
        }
        $em->flush($unit);
    }

    /**
     * @Given there are Ingredient with:
     */
    public function thereAreIngredientWith(TableNode $table)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        foreach($table->getHash() as $hash) {
            $unit = empty($hash['unit']) ? null : $em->getRepository('App\Entity\Unit')->findOneBy(['symbol' => $hash['unit']]);
            $ingredient = new Ingredient($hash['name'], $unit);
            $em->persist($ingredient);
        }
        $em->flush($ingredient);
    }

    /**
     * @Given there are ConversionRule with:
     */
    public function thereAreConversionruleWith(TableNode $table)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        foreach($table->getHash() as $hash) {
            $unitFrom = empty($hash['from']) ? null : $em->getRepository('App\Entity\Unit')->findOneBy(['symbol' => $hash['from']]);
            $unitTo = empty($hash['to']) ? null : $em->getRepository('App\Entity\Unit')->findOneBy(['symbol' => $hash['to']]);
            $conversionRule = new ConversionRule($unitFrom, $hash['factor'], $unitTo);
            $em->persist($conversionRule);
        }
        $em->flush($conversionRule);
    }

    /**
     * @Given there are Recipes with:
     */
    public function thereAreRecipesWith(TableNode $table)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        foreach($table->getHash() as $hash) {
            $recipe = new Recipe($hash['name'], [], $hash['isIngredient']);
            $em->persist($recipe);
        }
        $em->flush($recipe);
    }

    /**
     * @Given there Quantities for Recipe :id with:
     */
    public function thereQuantitiesForRecipeWith($id, TableNode $table)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        $recipe = $em->getRepository('App\Entity\Recipe')->find($id);
        $qtyList = [];
        foreach($table->getHash() as $hash) {
            $unit = $em->getRepository('App\Entity\Unit')->findOneBy(['symbol' => $hash['unit']]);
            $ingredient = $em->getRepository('App\Entity\Ingredient')->findOneBy(['name' => $hash['ingredient']]);
            $qtyList[] = new Quantity($hash['amount'], $unit, $ingredient);
        }
        $recipe->replaceQuantities($qtyList);
        $em->persist($recipe);
        $em->flush();
    }

    /**
     * @Given there is a RecipeList with recipes :idList
     */
    public function thereIsARecipelistWithRecipes($idList)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        $recipe = $em->getRepository('App\Entity\Recipe')->findById($idList);
        $rl = new RecipeList($recipe);
        $em->persist($rl);
        $em->flush();        
    }


    /**
     * @Then there should be :expected :class with:
     */
    public function thereShouldBeNoWith($expected, $class, TableNode $table)
    {
        $appKernel = $this->getKernel();
        $em = $appKernel->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('App\\Entity\\'.$class);
        $filters = [];
        foreach($table->getRows() as $row) {
            $filters[$row[0]] = $row[1];
        }
        $models = $repo->findBy($filters);
        if($expected != count($models)) {
            throw new \Exception(sprintf('Wrong count of %s. Expected %s but got %s', $class, $expected, count($models)));
        }
    }


}