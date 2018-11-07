@task
@task_edit

Feature: As an connected user, I need to able edit a task.

  Scenario: [Fail] The User is redirected if he isn't connected.
    Given I am on "/tasks/1/edit"
    Then I should be on "/login"

  Scenario: [Fail] The User is redirected to the tasks list page because the task id is wrong.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I have saved tasks
    And I am on "/tasks/10/edit"
    Then I should be on "/tasks"

  Scenario: [Fail] The User is on the tasks edition page, and he submit form without data.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I have saved tasks
    And I am on "/tasks/1/edit"
    When I fill in the following:
      | task_title   |  |
      | task_content |  |
    And I press "Modifier"
    Then I should be on "/tasks/1/edit"
    And I should see "Le titre de la tâche doit être renseigné."
    And I should see "Le contenu de la tâche doit être renseigné."

  Scenario: [Fail] The User is on the tasks edition page, and he submit form with a to short datas.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I have saved tasks
    And I am on "/tasks/1/edit"
    When I fill in the following:
      | task_title   | j |
      | task_content | j |
    And I press "Modifier"
    Then I should be on "/tasks/1/edit"
    And I should see "Le titre de la tâche doit comporter au moins 3 caractères."
    And I should see "Le contenu de la tâche doit comporter au moins 5 caractères."

  Scenario: [Fail] The User is on the tasks edition page, and he submit form with a to long datas.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I have saved tasks
    And I am on "/tasks/1/edit"
    When I fill in "task_title" with "jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj"
    And I press "Modifier"
    Then I should be on "/tasks/1/edit"
    And I should see "Le titre de la tâche ne doit pas comporter plus de 255 caractère."

  Scenario: [Success] The User is redirected to the tasks list page after the task is modified.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I have saved tasks
    And I am on "/tasks/1/edit"
    When I fill in the following:
      | task_title   | Nouveau titre   |
      | task_content | Nouveau contenu |
    And I press "Modifier"
    Then I should be on "/tasks"
    And I should see "Nouveau titre"
    And I should see "Nouveau contenu"