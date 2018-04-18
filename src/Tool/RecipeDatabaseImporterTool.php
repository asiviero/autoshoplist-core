<?php

namespace App\Tool;

use App\Entity\Unit;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Entity\Ingredient;
use App\Entity\ConversionRule;
use Symfony\Component\Yaml\Yaml;

class RecipeDatabaseImporterTool
{
    private $em;
    private $units = [];
    private $ingredients = [];
    private $errors = [];
    private $conversionRules = [];
    private $recipes = [];

    public function __construct($em) {
        $this->em = $em;
    }

    public function import($filename)
    {
        $yaml = Yaml::parse(file_get_contents($filename));
        $this->importIngredients($yaml['ingredients']);
        $this->importUnits($yaml['units']);
        $this->importConversionRules($yaml['conversion rules']);
        $this->importRecipes($yaml['recipes']);
        
        if(!empty($this->errors)) {
            throw new \Exception(implode(',', $errors));
        }
        $em = $this->em;
        array_walk($this->ingredients, function($item) use ($em) {
            $em->persist($item);
        });
        array_walk($this->units, function($item) use ($em) {
            $em->persist($item);
        });
        array_walk($this->conversionRules, function($item) use ($em) {
            $em->persist($item);
        });
        array_walk($this->recipes, function($item) use ($em) {
            $em->persist($item);
        });
        $em->flush();
    }

    private function importUnits($units)
    {
        foreach($units as $unit) {
            list($name, $symbol) = sscanf($unit, '%s (%[^)])');
            $this->units[$symbol] = new Unit($name, $symbol);
        }
    }

    private function importConversionRules($conversionRules)
    {
        foreach($conversionRules as $rule)
        {
            list($from, $factor, $to, $ingredient) = sscanf($rule, '1 %s = %f %s (%[^)])');
            $this->conversionRules[] = new ConversionRule(
                is_null($ingredient) ? null : $this->ingredients[$ingredient],
                $this->units[$from],
                $factor,
                $this->units[$to]
            );
        }
    }

    private function importIngredients($ingredients)
    {
        foreach($ingredients as $ingredient) {
            $this->ingredients[$ingredient] = new Ingredient($ingredient, null);
        }
    }

    private function importRecipes($recipes)
    {
        foreach($recipes as $name => $recipe)
        {
            $ingredientList = array_map(function($item) {
                list($amount, $unit, $ingredient) = explode(' ', $item, 3);
                return new Quantity(
                    $amount,
                    $this->units[$unit],
                    $this->ingredients[$ingredient]
                );
            }, $recipe['ingredient list']);
            $isIngredient = false;
            $unit = null;
            $factor = null;
            if(!empty($recipe['is ingredient'])) {
                $isIngredient = true;
                list($factor, $unit) = sscanf($recipe['is ingredient'], 'makes %f %s');
                $unit = $this->units[$unit];
            }
            $recipe = new Recipe($name, $ingredientList, $isIngredient, $unit, $factor);
            $this->recipes[] = $recipe;
            if($recipe->isIngredient()) {
                $this->ingredients[$name] = $recipe->getIngredient();
            }
        }
    }
}