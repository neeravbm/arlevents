Feature: 104-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and check government content access

  @javascript
  Scenario: Authenticated User as admin, staff or government role can access government document.
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29159/dashboard"
    Then I click "View All Communities"
    Then I should be redirected to "all-communities?destination=user/29159/dashboard"
    Then I click "Public Community Created by Supper Admin"
    Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Then I click "Archives"
    Then I should be redirected to "archives/10183"
    Then I click "Government Only And Archive Document Created by Supper Admin"
    Then I should be redirected to "groups/document/private-community-created-supper-admin/government-only-and-archive-document-created"
    #logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "View All Communities"
    Then I should be redirected to "all-communities?destination=user/9622/dashboard"
    Then I click "Public Community Created by Supper Admin"
    Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Then I should see "There is no document associated with this committee."
    Given I am on "groups/document/private-community-created-supper-admin/government-only-and-archive-document-created"
    Then I should see "You are not authorized to access this page."
    #need to change
    #logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "zzgovernmenttest1@gsa.govzz"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27275/dashboard"
    Then I click "View All Communities"
    Then I should be redirected to "all-communities?destination=user/27275/dashboard"
    Then I click "Public Community Created by Supper Admin"
    Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Then I should see "There is no document associated with this committee."
    Given I am on "groups/document/private-community-created-supper-admin/government-only-and-archive-document-created"
    Then I should see "Government Only And Archive Document Created by Supper Admin"
    #need to change
    #logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "mark@samblanet.com"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27104/dashboard"
    Then I click "View All Communities"
    Then I should be redirected to "all-communities?page=1&destination=user/27104/dashboard"
    Then I click "Public Community Created by Supper Admin"
    Then I should be redirected to "groups/community/public-community-created-supper-admin"
    Then I click "Archives"
    Then I should be redirected to "archives/10183"
    Then I click "Government Only And Archive Document Created by Supper Admin"
    Then I should be redirected to "groups/document/private-community-created-supper-admin/government-only-and-archive-document-created"
    

