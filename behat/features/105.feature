Feature: 105-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and create content

  @javascript
  Scenario: Authenticated User as admin, staff or member( community member) role can access archives/non-archives pages.
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29588/dashboard"
    Then I click "View All Communities"
    Then I should be redirected to "all-communities?destination=user/29588/dashboard"
    Then I click "Public Community Created by Supper Admin"
    Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Then I click "Archives"
    Then I should be redirected to "archives/10183"
    Then I should see "Government Only And Archive Document Created by Supper Admin"
    # logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "mark@samblanet.com"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27104/dashboard"
    #Then I click "View All Communities"
    #Then I should be redirected to "all-communities?page=1&destination=user/27104/dashboard"
    #Then I click "Public Community Created by Supper Admin"
    #Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Given I am on "groups/community/public-community-created-supper-admin"
    Then I click "Archives"
    Then I should be redirected to "archives/10183"
    Then I should see "Government Only And Archive Document Created by Supper Admin"
    
