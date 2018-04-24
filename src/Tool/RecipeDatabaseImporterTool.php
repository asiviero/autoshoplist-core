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

    private $ingredientRepo;
    private $unitRepo;
    private $conversionRepo;

    public function __construct($em) {
        $this->em = $em;
        $this->ingredientRepo = $this->em->getRepository('App\Entity\Ingredient');
        $this->unitRepo = $this->em->getRepository('App\Entity\Unit');
        $this->conversionRepo = $this->em->getRepository('App\Entity\ConversionRule');
        $this->recipeRepo = $this->em->getRepository('App\Entity\Recipe');
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
            $un = $this->unitRepo->findOneBy([
                'name' => $name,
                'symbol' => $symbol
            ]);
            if(!$un) {
                $un = new Unit($name, $symbol);
            }
            $this->units[$symbol] = $un;
        }
    }

    private function importConversionRules($conversionRules)
    {
        foreach($conversionRules as $rule)
        {
            list($from, $factor, $to, $ingredient) = sscanf($rule, '1 %s = %f %s (%[^)])');
            $filters = [
                'from' => $this->getUnit($from),
                'to' => $this->getUnit($to),
            ];
            $filters['ingredient'] = $ingredient ? $this->getIngredient($ingredient) : null;            
            $cr = $this->conversionRepo->findOneBy($filters);
            if(!$cr) {
                $cr = new ConversionRule(
                    $this->getUnit($from),
                    $factor,
                    $this->getUnit($to),
                    is_null($ingredient) ? null : $this->getIngredient($ingredient)
                );
            } else {
                $cr->setFactor($factor);
            }
            $this->conversionRules[] = $cr;
        }
    }

    private function getUnit($unit)
    {
        if(!isset($this->units[$unit])) {
            $un = $this->unitRepo->findOneBySymbol($unit);
            if(!$un) {
                $this->errors[] = sprintf('Unit not found: %s', $unit);
            } else {
                $this->units[$unit] = $un;
            }
        }
        return $this->units[$unit];
    }

    private function getIngredient($ingredient)
    {
        if(!isset($this->ingredients[$ingredient])) {
            $in = $this->ingredientRepo->findOneByName($ingredient);
            if(!$in) {
                $this->errors[] = sprintf('Ingredient not found: %s', $in);
            } else {
                $this->ingredients[$ingredient] = $in;
            }
        }
        return $this->ingredients[$ingredient];
    }

    private function importIngredients($ingredients)
    {
        foreach($ingredients as $ingredient) {
            $in = $this->ingredientRepo->findOneByName($ingredient);
            if(!$in) {
                $in = new Ingredient($ingredient, null);
            }
            $this->ingredients[$ingredient] = $in;
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
                    $this->getUnit($unit),
                    $this->getIngredient($ingredient)
                );
            }, $recipe['ingredient list']);
            $re = $this->recipeRepo->findOneByName($name);
            if($re) {
                $oldQty = $re->getQuantities();
                $em = $this->em;
                $oldQty->map(function($item) use($em) {
                    $em->remove($item);
                });
                $re->replaceQuantities($ingredientList);
                $this->recipes[] = $re;
            } else {
                $isIngredient = false;
                $unit = null;
                $factor = null;
                if(!empty($recipe['is ingredient'])) {
                    $isIngredient = true;
                    list($factor, $unit) = sscanf($recipe['is ingredient'], 'makes %f %s');
                    $unit = $this->getUnit($unit);
                }
                $recipe = new Recipe($name, $ingredientList, $isIngredient, $unit, $factor);
                $this->recipes[] = $recipe;
                if($recipe->isIngredient()) {
                    $this->ingredients[$name] = $recipe->getIngredient();
                }
            }

        }
    }
}