Feature: Recipe Scenario

    Background:
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|
            |gram|g|
            |cup|cup|
        And there are Ingredient with:
            |name|unit|
            |tomato|kg|
        And there are Recipes with:
            |name|isIngredient|
            |tomato sauce|0|
        And there Quantities for Recipe 1 with:
            |ingredient|amount|unit|
            |tomato|200|g|
        
    Scenario: Retrieve Recipe
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/recipe/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        | name                   | tomato sauce              |
        And the JSON node "quantities" should have 1 element

    Scenario: Create Recipe
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/recipe/" with body:
        """
            {
                "name" : "new tomato sauce",
                "quantities" : [
                    {
                        "amount" : 300,
                        "ingredient" : {
                            "id" : 1,                    
                            "name" : "tomato",
                            "baseUnit" : {
                                "id" : 1,
                                "name" : "kilogram",
                                "symbol" : "kg"
                            }
                        },
                        "unit" : {
                            "id" : 1,
                            "name" : "kilogram",
                            "symbol" : "kg"                
                        }
                    }
                ]
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        | name                   | new tomato sauce              |
        And the JSON node "quantities" should have 1 element
        And the JSON node "quantities[0].ingredient.name" should contain "tomato"
        And the JSON node "quantities[0].amount" should contain "300"
        And the JSON node "quantities[0].unit.symbol" should contain "kg"

    Scenario: Edit Recipe
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/recipe/1" with body:
        """
            {
                "name" : "new tomato sauce",
                "quantities" : [
                    {
                        "amount" : 300,
                        "ingredient" : {
                            "id" : 1,                    
                            "name" : "tomato",
                            "baseUnit" : {
                                "id" : 1,
                                "name" : "kilogram",
                                "symbol" : "kg"
                            }
                        },
                        "unit" : {
                            "id" : 1,
                            "name" : "kilogram",
                            "symbol" : "kg"                
                        }
                    }
                ]
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        | id|1|
        | name                   | new tomato sauce              |
        And the JSON node "quantities" should have 1 element
        And the JSON node "quantities[0].ingredient.name" should contain "tomato"
        And the JSON node "quantities[0].amount" should contain "300"
        And the JSON node "quantities[0].unit.symbol" should contain "kg"


    Scenario: Delete Recipe
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "DELETE" request to "/api/recipe/1"
        Then the response status code should be 200
        And there should be 0 "Recipe" with:
            |name|tomato sauce|