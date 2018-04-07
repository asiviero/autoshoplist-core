<?php

namespace App\Tests;

use App\Entity\Ingredient;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DatabaseTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function setUp()
    {        
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        if(getenv('DATABASE_URL') == 'sqlite://:memory:') {
            $schemaTool = new SchemaTool($this->entityManager);
            $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}