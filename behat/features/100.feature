Feature: 100-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and create content

  @javascript @insulated
  Scenario: Authenticated User as staff role
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29159/dashboard"
    Then I click "View All Communities"
    Then I should be redirected to "all-communities?page=1&destination=user/29159/dashboard"
    Then I click "Public Community Created by Supper Admin"
    Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Then I should see "Update from NetForum"
   