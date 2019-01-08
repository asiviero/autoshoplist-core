# autoshoplist-core

[![Build Status](https://travis-ci.org/asiviero/autoshoplist-core.svg?branch=master)](https://travis-ci.org/asiviero/autoshoplist-core)

Base code and API for autoshoplist, a project to generate shopping lists
based on recipes and their quantities. A shopping list will be the sum of all
quantities of ingredients in a list of recipes, taking into account some 
recipes may be ingredients as well (e.g. tomato sauce).

It is built in Symfony 4 and mostly intended as a laboratory to myself and
friends. As of right now there is no front-end, only http calls and some
commands that make it usable.

## Install

A Vagrantfile is provided, so `$ vagrant up` should be enough to get it 
installed. After that, you'll need to get into the box wih `$ vagrant ssh`
and run:

```
$ cd /vagrant/
$ php bin/console server:start
```

Then all the endpoints should be available under http://127.0.0.1:8001/

## General Structure

The following entities are the base of this project:

- `Unit`: An unit of measurement, with name and symbol
- `Ingredient`: An ingredient, which may be a recipe as well, and possibly with a preferred `Unit`
- `ConversionRule`: A conversion rule between 2 units, and possibly restricted by ingredient
- `Quantity`: A combination of `Ingredient`, `Unit` and an amount
- `Recipe`: A list of `Quantity`, which may also be an ingredient, and may have a code to be more easily identified on CLI list generation
- `RecipeList`: A list of `Recipe`

Through the CLI, a recipe list is what generates a shopping list, via the 
command `autoshoplist:recipe-list:generate`. All the `Quantity` of its 
recipes will be summed recursively and generate a single list, grouped by
`Ingredient` and, if possible, in the preferred `Unit` for the ingredient:

```
$ php bin/console autoshoplist:recipe-list:generate "fettucine al pomodoro" "rustic braised chicken with mushrooms"
Generated Recipe List with Id: 1
2.00 cup flour
3.00 un egg
0.10 kg salt
8.00 un tomato
0.06 l olive oil
4.00 un bacon
2.00 un chicken breast
1.00 un white mushroom
1.00 un onion
0.25 cup tomato paste
3.00 un garlic
2.00 tsp thyme
0.25 cup parsley
1.00 tbsp red wine vinegar
```
Add more recipes by name or code to the end of the command to generate larger lists.

## Importing the Database

The data from the system can be imported using the `autoshoplist:import-database` command, using a yaml file as input. Some 
examples of these files are included both in the `tests` and in the `assets` 
folder. The syntax for each section is:

- `units`: name (symbol)
- `ingredients`: name [(unit.symbol)]
- `conversion rule`: 1 unit.symbol = factor unit.symbol [(ingredient.name)]
- `recipes`:
    - name:
        - `code`: string 
        - `ingredient list`: amount unit.symbol ingredient.name
        - `is ingredient`: makes amount unit.symbol

Finally, the command to import the database is:

```
$ php bin/console autoshoplist:import-database assets/base.yml
```

In case of a repeated unit or ingredient, the command will simply ignore it.
Conversion Rules and Recipes will be updated.

## API Documentation

The API documentation is generated dynamically from the annotations in the 
files. After running the server inside the box, access 
`http://127.0.0.1:8001/api/doc` to see the full docs and 
`http://127.0.0.1:8001/api/doc.json` for the pure json which can be used by
Swagger to generate a client lib.

## Known limitations

Apart from no front-end, obviously, there is no security for the API and no
support for multiuser. It'd make it a bit more complex than what I needed it 
for, so I set that aside for now. If you'd like to develop any of these, it'd
be really appreciated.

Since it's also not aimed at becoming a product, I have not included things
that would make it more pleasant to use futurely, such as images for 
ingredients and the how-tos for the recipes. These are not relevant for the
purpose of generating a shopping list but could be added in an easy way.

As this project is largely also a laboratory for learning Symfony 4 and its
resources, I acknowlege there might be a lot of room for improvement in code
quality. Any help in that direction is also appreciated.

## Tests

There are phpunit and behat tests. Phpunit will test the methods in repository and models and behat will test the API. To run them:

```
$ ./vendor/bin/simple-phpunit tests/
$ php vendor/bin/behat features/
```

## License

MIT