Feature: Conversion Rule related tests

    Background:
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|
            |gram|g|
            |cup|cup|
        And there are Ingredient with:
            |name|unit|
            |tomato|kg|
        And there are ConversionRule with:
            |from|factor|to|
            |kg|1000|g|

    Scenario: Retrieve Conversion Rule        
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/conversionRule/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        | factor                   | 1000              |
        And the JSON node "from.symbol" should contain "kg"
        And the JSON node "to.symbol" should contain "g"

    Scenario: Create new conversion rule
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/conversionRule/" with body:
        """
            {
                "ingredient" : {
                    "id" : 1,                    
                    "name" : "tomato",
                    "baseUnit" : {
                        "id" : 1,
                        "name" : "kilogram",
                        "symbol" : "kg"
                    }
                },
                "from" : {
                    "id" : 1,
                    "name" : "kilogram",
                    "symbol" : "kg"
                },
                "to" : {
                    "id" : 3,
                    "name" : "cup",
                    "symbol" : "cup"
                },
                "factor" : 200
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        |id|    2|
        | factor                   | 200              |
        And the JSON node "from.symbol" should contain "kg"
        And the JSON node "to.symbol" should contain "cup"
        And the JSON node "ingredient.name" should contain "tomato"
        And there should be 1 "ConversionRule" with:
            |id|2|
            |factor|200|

    Scenario: Edit conversion rule
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/conversionRule/1" with body:
        """
            {
                "ingredient" : {
                    "id" : 1,                    
                    "name" : "tomato",
                    "baseUnit" : {
                        "id" : 1,
                        "name" : "kilogram",
                        "symbol" : "kg"
                    }
                },
                "from" : {
                    "id" : 1,
                    "name" : "kilogram",
                    "symbol" : "kg"
                },
                "to" : {
                    "id" : 2,
                    "name" : "g",
                    "symbol" : "g"
                },
                "factor" : 500
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And print last JSON response
        And the JSON nodes should contain:
        |id|    1|
        | factor                   | 500              |
        And the JSON node "from.symbol" should contain "kg"
        And the JSON node "to.symbol" should contain "g"
        And the JSON node "ingredient.name" should contain "tomato"
        And there should be 1 "ConversionRule" with:
            |id|1|
            |factor|500|

    Scenario: Delete conversion rule
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "DELETE" request to "/api/conversionRule/1"
        Then the response status code should be 200
        And there should be 0 "ConversionRule" with:
            |factor|1000|