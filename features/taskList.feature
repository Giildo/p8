@task
@task_list

Feature: As an connected user, I need to able show the tasks.

  Scenario: [Fail] The User is redirected if he isn't connected.
    Given I am on "/tasks"
    Then I should be on "/login"

  Scenario: [Success] The User is on the tasks list page, but no task saved.
    Given I load a specific user
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/tasks"
    Then I should see "Il n'y a pas encore de tâche enregistrée."
    And I should be on "/tasks"

  Scenario: [Success] The User is on the tasks list page, he see tasks.
    Given I have saved tasks
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/tasks"
    Then I should see "Titre de la première tâche"
    And I should see "Titre de la seconde tâche"
    And I should be on "/tasks"

  Scenario: [Success] The User is on the tasks list page, he deletes a task.
    Given I have saved one task
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/tasks"
    When I press "Supprimer"
    Then I should see "Il n'y a pas encore de tâche enregistrée."
    And I should be on "/tasks"

  Scenario: [Success] The User is on the tasks list page, he marks the task as done.
    Given I have saved one task
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/tasks"
    When I press "Marquer comme faite"
    Then I should see "La tâche \"Titre de la première tâche\" a bien été marquée comme terminée."

  Scenario: [Success] The User is on the tasks list page, he marks the task as not done.
    Given I have saved one task
    And I am logged with username "JohnDoe" and with password "12345678"
    And I am on "/tasks"
    When I press "Marquer comme faite"
    And I press "Marquer non terminée"
    Then I should see "La tâche \"Titre de la première tâche\" a bien été marquée comme non terminée."