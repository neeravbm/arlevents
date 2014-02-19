Feature: 112-Create features for ACT-IAC project
  In order to check if user has access the dashboard
  As an authenticated user
  I need to be able login and register

  @javascript
  Scenario:  Authenticated user can access registration badge on checkout if product in cart should be only one among product,tracks or session. Ignore invoice products.
    Given I am on "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Given I am on "cart"
    Then I should see "Test Event 70"
    Then I press button "Confirm Registration"
    Then I should be redirected to "checkout/120"
    Then I should see "Badge Registration"
    Then I press button "Cancel"
    Then I should be redirected to "cart"
    Then I click "Igniting Innovation 2014 - February 6, 2014"
    Then I should be redirected to "ignitinginnovation2014"
    Then I press button "Register Now"
    Then I click "your cart"
    Then I should be redirected to "cart"
    Then I press button "Confirm Registration"
    Then I should be redirected to "checkout/120"
    Then I should not see "Badge Registration"

    
    
    
