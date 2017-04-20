Feature: Steps for single stores work correctly on postgresql

  Background:
    Given the following "postgresql_company" data exists:
      | name      |
      | Coupla    |
      | LaunchKey |

    Given the following "postgresql_user" data exists:
      | username | postgresql_company   |
      | Adam     | name: Coupla    |
      | Devin    | name: LaunchKey |

  Scenario: The company data exists
    Then the following "postgresql_company" data is found:
      | name   |
      | Coupla |

  Scenario: Only the provided company data exists
    Then only the following "postgresql_company" data is found:
      | name      |
      | Coupla    |
      | LaunchKey |

  Scenario: The user data exists
    Then the following "postgresql_user" data is found:
      | username |
      | Adam     |

  Scenario: Only the provided user data exists
    Then only the following "postgresql_user" data is found:
      | username |
      | Adam     |
      | Devin    |

  Scenario: Find user data by company
    Then the following "postgresql_user" data is found:
      | postgresql_company |
      | name: Coupla  |

  Scenario: Wiping all data wipes all data
    When I wipe all "postgresql_company" data
    And I wipe all "postgresql_user" data
    Then there is no "postgresql_company" data
    And there is no "postgresql_user" data

