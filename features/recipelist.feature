Feature: RecipeList feature
    
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
    And there is a RecipeList with recipes "1"

    Scenario: Retrieve Recipe List
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/recipeList/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON node "recipes" should have 1 element

    Scenario: Create Recipe List
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/recipeList/" with body:
        """
            {
                "recipes" : [
                    {
                        "id" : 1
                    }
                ]
            }
        """
        And print last JSON response
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON node "recipes" should have 1 element
        And the JSON node "recipes[0].id" should contain "1"
        And the JSON node "id" should contain "2"

    Scenario: Update Recipe List
        And there are Recipes with:
            |name|isIngredient|
            |new tomato sauce|0|
        And there Quantities for Recipe 2 with:
            |ingredient|amount|unit|
            |tomato|400|g|
        
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/recipeList/1" with body:
        """
            {
                "recipes" : [
                    {
                        "id" : 2
                    }
                ]
            }
        """
        And print last JSON response
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON node "recipes" should have 1 element
        And the JSON node "recipes[0].id" should contain "2"
        And the JSON node "id" should contain "1"

    Scenario: Delete Recipe List
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "DELETE" request to "/api/recipeList/1"
        Then the response status code should be 200
        And there should be 0 "RecipeList" with:
            |id|1|