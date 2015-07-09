Feature: Checking if a dark launch feature is enabled
  In order to use dark launching
    As a developer
    I need to be able to check if a feature is enabled

  Scenario: I check a feature and it is enabled
    And I call get_feature with 'feature-1'
    Then I should get TRUE

  Scenario: I check a feature and it is not enabled
    And I call get_feature with 'feature-2'
    Then I should get FALSE