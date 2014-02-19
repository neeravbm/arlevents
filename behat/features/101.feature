Feature: 101-Create features for ACT-IAC project
  In order to check if user has access the site
  As a staff roles
  I need to be able login and see Update from NetForum link on user profile page

  @javascript
  Scenario: Authenticated User as staff role can access Update from NetForum at user profile page
    Given I am on "/"
    Then I fill in "username" as "staff.actiac@gmail.com"
    Then I fill in password "password" as "admin123"
    Then I press button "Log in"
    Then I should be redirected to "user/29159/dashboard"
    Then I click "Staff"
    Then I should be redirected to "user"
    Then I should see "Update from NetForum"
    Then I click "Logout"
    Then I should be redirected to "/"
    #login by authenticated user and try to see update from NetForum link
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "Mark"
    Then I should be redirected to "user"

