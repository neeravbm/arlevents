Feature: 109-Create features for ACT-IAC project
  In order to check if user has access the site
  As an administer role roles
  I need to be able login and access 'administer group' tab

  @javascript
  Scenario: Authenticated User as administer role can access tab Administer Group.
    Given I am on "/"
    Then I fill in "username" as "mark@samblanet.com"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27104/dashboard"
    Given I am on "groups/community/public-community-created-supper-admin"
    Given I am on "node/13893/group"
    Then I should see "Public Community Created by Supper Admin"
    Given I am on "groups/event/public-event-created-supper-admin"
    Given I am on "node/13896/group"
    Then I should see "Public Event Created by Supper Admin"
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
    
    
    
