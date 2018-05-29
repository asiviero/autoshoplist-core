Feature: Ingredient related tests

    Scenario: Retrieve Ingredient
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|        
        Given there are Ingredient with:
            |name|unit|
            |tomato|kg|
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/ingredient/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        | name                   | tomato              |
        And the JSON node "baseUnit.symbol" should contain "kg"

    Scenario: Create Ingredient without base unit
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|                
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/ingredient/" with body:
        """
            {
                "name" : "tomato"
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        |id|    1|
        | name                   | tomato              |
        And the JSON node "baseUnit" should be null

    Scenario: Create Ingredient with base unit
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|                
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/ingredient/" with body:
        """
            {
                "name" : "tomato",
                "baseUnit" : {
                    "id" : 1,
                    "name" : "kilogram",
                    "symbol" : "kg"
                }
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        |id|    1|
        | name                   | tomato              |
        And the JSON node "baseUnit.symbol" should contain "kg"

    Scenario: Create Ingredient with base unit fails if invalid unit
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|                
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/ingredient/" with body:
        """
            {
                "name" : "tomato",
                "baseUnit" : {
                    "id" : 2,
                    "name" : "cup",
                    "symbol" : "cup"
                }
            }
        """
        Then the response status code should be 500        
        And there should be 0 Ingredient with:
            |name|tomato|

    Scenario: Edit Ingredient
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|
            |cup|cup|
        Given there are Ingredient with:
            |name|unit|
            |tomato|kg|        
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/ingredient/1" with body:
        """
            {
                "name" : "milk",
                "baseUnit" : {
                    "id" : 2,
                    "name" : "cup",
                    "symbol" : "cup"
                }
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON nodes should contain:
        |id|1|
        | name                   | milk              |
        And the JSON node "baseUnit.symbol" should contain "cup"

    Scenario: Delete Unit
        Given there are Ingredient with:
            |name|unit|
            |tomato|kg|        
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "DELETE" request to "/api/ingredient/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And there should be 0 "Ingredient" with:
            |name|tomato|