@clean @api @commons
Feature: Searching for files on local wiki stored on commons
  Scenario: A file that exists only on commons can be found on the local wiki
    When I api search in namespace 6 for commons
    Then File:OnCommons.svg is the first api search result

  Scenario: A file that exists on commons and the local wiki returns the local result
    When I api search in namespace 6 for duplicated
    Then File:DuplicatedLocally.svg is the first api search result
    And Locally stored file *duplicated* on commons is the highlighted snippet of the first api search result
    And there is no second api search result
