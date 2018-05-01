Feature: Unit related tests

    Scenario: Retrieve Unit
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/unit/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON nodes should contain:
        | name                   | kilogram              |
        | symbol                 | kg                    |

    Scenario: Create Unit
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/unit/" with body:
        """
            {
                "symbol" : "kg",
                "name" : "kilogram"
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON nodes should contain:
        |id|1|
        | name                   | kilogram              |
        | symbol                 | kg                    |

    Scenario: Edit Unit
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/unit/1" with body:
        """
            {
                "symbol" : "kg2",
                "name" : "kilogram2"
            }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And the JSON nodes should contain:
        |id|1|
        | name                   | kilogram2              |
        | symbol                 | kg2                    |

    Scenario: Delete Unit
        Given there are Unit with:
            |name|symbol|
            |kilogram|kg|
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "DELETE" request to "/api/unit/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the header "Content-Type" should be equal to "application/json"
        And there should be 0 "Unit" with:
            |name|kilogram|
            |symbol|kg|