@home_page

Feature: As an connected user, I need to able access to the home page.

  Scenario: [Fail] The user is redirected to the connection page if he isn't connected.
    Given I am on "/"
    Then I should be on "/login"

  Scenario: [Fail] The User is on the home page if he's connected.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/"
    Then I should be on "/"
    And I should see "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !"