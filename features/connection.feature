@user
@user_connection

Feature: As an anonymous user, I need to be able to logged in on application.

  Scenario: [Fail] The User is redirected if he is already logged in.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/login"
    Then I should be on "/"

  Scenario: [Fail] The User submit the form without data.
    Given I am on "/login"
    When I press "Valider"
    Then I should see "Le nom d'utilisateur n'est pas valide."
    And I should be on "/login"

  Scenario: [Fail] The User submit the form with a wrong username.
    Given I am on "/login"
    And I load a specific user
    When I press "Valider"
    And I fill in "connection_username" with "wrong"
    Then I should see "Le nom d'utilisateur n'est pas valide."
    And I should be on "/login"

  Scenario: [Fail] The User submit the form with a wrong password.
    Given I am on "/login"
    And I load a specific user
    When I fill in the following:
      | connection_username | JohnDoe |
      | connection_password | wrong |
    And I press "Valider"
    Then I should see "Le mot de passe n'est pas valide."
    And I should be on "/login"

  Scenario: [Success] The User is logged in as successfully
    Given I am on "/login"
    And I load a specific user
    When I fill in the following:
      | connection_username | JohnDoe  |
      | connection_password | 12345678 |
    And I press "Valider"
    Then I should be on "/"
