Feature: 106-Create features for ACT-IAC project
  In order to check if user has access the site
  As a various different roles
  I need to be able login and access government only document

  @javascript
  Scenario: Authenticated User as admin, staff or government role can access government document on knowledge page.
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29163/dashboard"
    #Then I click "Knowledge Bank"
    #Then I should be redirected to "knowledge-bank"
    Given I am on "knowledge-bank"
    Then I should see "Government Only And Archive Do..."
    # logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "zzgovernmenttest1@gsa.govzz"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27275/dashboard"
    #Then I click "Knowledge Bank"
    #Then I should be redirected to "knowledge-bank"
    Given I am on "knowledge-bank"
    Then I should see "Government Only And Archive Do..."
    # logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "mark@samblanet.com"
    Then I fill in password "password" as "password"
    Then I press button "Log in"
    Then I should be redirected to "user/27104/dashboard"
    Then I click "Knowledge Bank"
    #Then I should be redirected to "knowledge-bank"
    Given I am on "knowledge-bank"
    Then I should see "Government Only And Archive Do..."
      # logout user
    Then I click "Logout"
    Then I should be redirected to "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "Knowledge Bank"
      #Then I should be redirected to "knowledge-bank"
    Given I am on "knowledge-bank"
    Then I should not see "Government Only And Archive Do..."
    
    
