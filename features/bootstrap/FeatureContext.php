<?php

use App\Kernel;
use App\Entity\Unit;
use App\Entity\Ingredient;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\Tools\SchemaTool;
use App\Entity\ConversionRule;

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