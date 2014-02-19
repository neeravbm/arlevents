Feature: 102-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and access community/event

  @javascript
  Scenario: Authenticated User as Member role can access private community/event, Non-member cannot access private community/event
    Given I am on "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "Private Community Created by Supper Admin"
    Then I should be redirected to "groups/community/private-community-created-supper-admin"
    Then I should see "Private Community Created by Supper Admin"
    #access user to non-member private community
    Given I am on "groups/community/private-non-member-community-created-supper-admin"
    Then I should see "You are not authorized to access this page."
    #access event by member user
    Then I click "Calendar of Events"
    Then I should be redirected to "calendar/month"
    Then I click "List Of Events"
    Then I should be redirected to "calendar/month/listing"
    Then I click "Private Member Event Created by Supper Admin"
    Then I should be redirected to "groups/event/private-member-event-created-supper-admin"
    Then I should see "Private Member Event Created by Supper Admin"
    #access user to non-member private event
    Given I am on "groups/event/private-non-member-event-created-supper-admin"
    Then I should see "You are not authorized to access this page."
    # access public community as member
    Then I click "Mark"
    Then I should be redirected to "user"
    Then I click "My Dashboard"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "Public Event Created by Supper Admin"
    Then I should be redirected to "groups/event/public-event-created-supper-admin"
    # access public event as member
    Then I click "Calendar of Events"
    Then I should be redirected to "calendar/month"
    Then I click "List Of Events"
    Then I should be redirected to "calendar/month/listing"
    Then I click "Public Event Created by Supper Admin"
    Then I should be redirected to "groups/event/public-event-created-supper-admin"



    

