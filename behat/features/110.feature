Feature: 110-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and access content

  @javascript
  Scenario:  Do not allow anyone other than the community member to “Attend” the meeting of committees that are private.
    Given I am on "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Given I am on "groups/community/private-community-created-supper-admin"
    Then I should see "Private Community Created by Supper Admin"
    Then I click "Private Meeting Created By Supper Admin"
    Then I should be redirected to "groups/event/private-meeting-created-supper-admin"
    Then I should see "Private Meeting Created By Supper Admin"
    Given I am on "groups/community/private-non-member-community-created-supper-admin"
    Then I should see "You are not authorized to access this page."

    
    
    
