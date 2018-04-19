<?php

namespace App\Command;

use Doctrine\ORM\EntityManager;
use App\Tool\RecipeDatabaseImporterTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ImportDatabaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('autoshoplist:import-database')
            ->setDescription('Read from YAML and map into database')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'Database filename'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();        
        $tool = new RecipeDatabaseImporterTool($entityManager);
        $tool->import($input->getArgument('database'));        
    }
}