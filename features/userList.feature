@user
@user_list

Feature: As an connected user, I need to able show the users.

  Scenario: [Fail] The User is redirected if he isn't connected.
    Given I am on "/users"
    Then I should be on "/login"

  Scenario: [Fail] The User is redirected if he doesn't have the rights.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/users"
    Then I should see "Vous n'avez pas les droits pour accéder à cette page."
    And I am on "/login"

  Scenario: [Success] The User is on the users list page, he see users.
    Given I load a specific user
    And I am logged with username "JaneDoe" and with password "12345678"
    And I am on "/users"
    Then I should see "JaneDoe"
    And I should see "JohnDoe"
    And I should see "john@doe.com"
    And I should see "jane@doe.com"
    And I should be on "/users"