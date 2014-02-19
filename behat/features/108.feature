Feature: 108-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and access 'tab access control'

  @javascript
  Scenario: Authenticated User who have permission 'view access control settings' can access tab 'access control' on community and event page.
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29163/dashboard"
    #Then I click "View All Communities"
    #Then I should be redirected to "all-communities?destination=user/29163/dashboard"
    #Then I click "Public Community Created by Supper Admin"
    #Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Given I am on "groups/community/public-community-created-supper-admin"
    Given I am on "node/13893/access"
    Then I should see "Access control for Public Community Created by Supper Admin"
    Given I am on "groups/event/public-event-created-supper-admin"
    Given I am on "node/13896/access"
    Then I should see "Access control for Public Event Created by Supper Admin"
    # logout and login by administer role
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "mark@samblanet.com"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27104/dashboard"
    #Then I click "View All Communities"
    #Then I should be redirected to "all-communities?destination=user/27104/dashboard"
    #Then I click "Public Community Created by Supper Admin"
    #Then I should be redirected to "groups/community/public-community-created-supper-admin"
    #Given I am on "node/10183/access"
    Given I am on "groups/community/public-community-created-supper-admin"
    Given I am on "node/13893/access"
    Then I should see "Access control for Public Community Created by Supper Admin"
    Given I am on "groups/event/public-event-created-supper-admin"
    Given I am on "node/13896/access"
    Then I should see "Access control for Public Event Created by Supper Admin"
    #logout and login by normal user.
    Then I click "Log out"
    Then I should be redirected to "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Given I am on "groups/community/public-community-created-supper-admin"
    Given I am on "node/13893/access"
    Then I should see "You are not authorized to access this page."
    Given I am on "groups/event/public-event-created-supper-admin"
    Given I am on "node/13896/access"
    Then I should see "You are not authorized to access this page."
    
    
    
