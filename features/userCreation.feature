@user
@user_creation

Feature: As an connected user, I need to able create a user.

  Scenario: [Fail] The User is redirected if he isn't connected.
    Given I am on "/users/create"
    Then I should be on "/login"

  Scenario: [Fail] The User is redirected if he doesn't have the rights.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/users/create"
    Then I should see "Vous n'avez pas les droits pour accéder à cette page."
    And I am on "/login"

  Scenario: [Fail] The User is on the users edition page, and he submit form without data.
    Given I load a specific user
    And I am logged with username "JaneDoe" and with password "12345678"
    And I am on "/users/create"
    When I fill in the following:
      | registration_username        |  |
      | registration_password_first  |  |
      | registration_password_second |  |
      | registration_email           |  |
    And I press "Ajouter"
    Then I should be on "/users/create"
    And I should see "Le nom d'utilisateur doit être renseigné"
    And I should see "Le mot de passe doit être renseigné"
    And I should see "L'email doit être renseigné"

  Scenario: [Fail] The User is on the users edition page, and he submit form with a to short datas.
    Given I load a specific user
    And I am logged with username "JaneDoe" and with password "12345678"
    And I am on "/users/create"
    When I fill in the following:
      | registration_username        | j |
      | registration_password_first  | j |
      | registration_password_second | j |
      | registration_email           | j |
    And I press "Ajouter"
    Then I should be on "/users/create"
    And I should see "Le nom d'utilisateur doit comporter au moins 2 caractères."
    And I should see "Le mot de passe doit comporter au moins 8 caractères."

  Scenario: [Fail] The User is on the users edition page, and he submit form with a wrong datas.
    Given I load a specific user
    And I am logged with username "JaneDoe" and with password "12345678"
    And I am on "/users/create"
    When I fill in the following:
      | registration_password_first  | j |
      | registration_password_second |   |
      | registration_email           | j |
    And I press "Ajouter"
    Then I should be on "/users/create"
    And I should see "This value is not valid."
    And I should see "Le format de l'email n'est pas valide."

  Scenario: [Fail] The User is on the users edition page, and he submit form with a to long datas.
    Given I load a specific user
    And I am logged with username "JaneDoe" and with password "12345678"
    And I am on "/users/create"
    When I fill in the following:
      | registration_username        | jjjjjjjjjjjjjjjjjjjjjjjjjjjjjj                                                             |
      | registration_password_first  | jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj |
      | registration_password_second | jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj |
      | registration_email           | jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj@jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj.fr |
    And I press "Ajouter"
    Then I should be on "/users/create"
    And I should see "Le nom d'utilisateur ne doit pas comporter plus de 25 caractères."
    And I should see "Le mot de passe ne doit pas comporter plus de 64 caractères."
    And I should see "L'email ne doit pas comporter plus de 60 caractères."

  Scenario: [Success] The User is redirected to the tasks list page after the task is modified.
    Given I load a specific user
    And I am logged with username "JaneDoe" and with password "12345678"
    And I am on "/users/create"
    When I fill in the following:
      | registration_username        | Giildo              |
      | registration_password_first  | 123456789           |
      | registration_password_second | 123456789           |
      | registration_email           | giildo.jm@gmail.com |
    And I select "ROLE_USER" from "registration_roles"
    And I press "Ajouter"
    Then I should be on "/users"
    And I should see "Giildo"
    And I should see "giildo.jm@gmail.com"