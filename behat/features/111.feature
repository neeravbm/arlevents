Feature: 111-Create features for ACT-IAC project
  In order to check if user has access the dashboard
  As an authenticated user except supper admin
  I need to be able login and access user's dashboard

  @javascript
  Scenario:  Authenticated user can access only own dashboard, for others it goes to user's public profile page.
    Given I am on "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "Donnald Becker"
    Then I should be redirected to "user/6612/public-profile"
    Then I should see "Donnald Becker"
    
    
    
