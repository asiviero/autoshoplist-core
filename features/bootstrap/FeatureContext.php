<?php

use App\Kernel;
use App\Entity\Unit;
use App\Entity\Ingredient;
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
        $ingredient = new Ingredient('tomato');
        $em->persist($ingredient);
        $em->flush();
        $repo = $em->getRepository('App\Entity\Ingredient');
        $all = $repo->findAll();
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