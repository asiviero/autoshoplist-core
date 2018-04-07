<?php

use App\Kernel;
use App\Entity\Ingredient;
use Behat\Behat\Context\Context;
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
        $c=1;
    }
}