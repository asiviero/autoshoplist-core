<?php

namespace App\Command;

use App\Entity\RecipeList;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RecipeListCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('autoshoplist:recipe-list:generate')
            ->setDescription('Generate a recipe list and output its shoplist')
            ->addArgument(
                'recipes',
                InputArgument::IS_ARRAY,
                'List of recipe names'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
        $recipeRepo = $entityManager->getRepository('App\Entity\Recipe');
        $recipes = $input->getArgument('recipes');
        $list = array_filter(array_map(function($recipe) use($recipeRepo) {
            $r = $recipeRepo->findOneByName($recipe);
            return $r;
        }, $recipes));

        $rlist = new RecipeList($list);
        $entityManager->persist($rlist);
        $entityManager->flush();
        
        $rlistRepo = $entityManager->getRepository('App\Entity\RecipeList');
        $qtys = $rlistRepo->getFlattenedQuantities($rlist);
        $output->writeln(sprintf("Generated Recipe List with Id: %s", $rlist->getId()));
        foreach($qtys as $qty) {
            $output->writeln($qty->__toString());
        }
    }
}