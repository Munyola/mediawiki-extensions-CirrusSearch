#
# This file is subject to the license terms in the COPYING file found in the
# CirrusSearch top-level directory and at
# https://phabricator.wikimedia.org/diffusion/ECIR/browse/master/COPYING. No part of
# CirrusSearch, including this file, may be copied, modified, propagated, or
# distributed except according to the terms contained in the COPYING file.
#
# Copyright 2012-2014 by the Mediawiki developers. See the CREDITS file in the
# CirrusSearch top-level directory and at
# https://phabricator.wikimedia.org/diffusion/ECIR/browse/master/CREDITS
#
@clean @firefox @test2.wikipedia.org @phantomjs @smoke
Feature: Smoke test

  @en.wikipedia.beta.wmflabs.org
  Scenario: Search suggestions
    Given I am at a random page
    When I search for: main
    Then a list of suggested pages should appear
      And Main Page should be the first result

  @expect_failure
  Scenario: Fill in search term and click search
    Given I am at a random page
    When I search for: ma
      And I click the search button
    Then I should land on Search Results page

  @en.wikipedia.beta.wmflabs.org
  Scenario: Search with accent yields result page with accent
    Given I am at a random page
    When I search for África
    Then the page I arrive on has title África
