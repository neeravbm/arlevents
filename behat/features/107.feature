Feature: 107-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and access send message link

  @javascript
  Scenario: Authenticated User as supper admin or staff role can access send message link on community page.
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29163/dashboard"
    Given I am on "groups/community/public-community-created-supper-admin"
    Then I should see "send Message"
    # logout user and login with supper admin
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "admin"
    Then I fill in password "password" as "laca21@89"
    Then I press button "Log in"
    Then I should be redirected to "user/1/dashboard"
    Given I am on "groups/community/public-community-created-supper-admin"
    Then I should see "send Message"
    #logout and login with administer role
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "mark@samblanet.com"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27104/dashboard"
    Given I am on "groups/community/public-community-created-supper-admin"
    Then I should see "send Message"



