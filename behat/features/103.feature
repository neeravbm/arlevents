Feature: 103-Create features for ACT-IAC project
  In order to check if user has access the site
  As a member user
  I need to be able login and access community's private content

  @javascript
  Scenario: Authenticated User as Member role can access community's private content, Non-member cannot access community's private content
    Given I am on "/"
    Then I fill in "username" as "zzmark@fedbizcoach.comzz"
    Then I fill in password "password" as "Pretzel-900"
    Then I press button "Log in"
    Then I should be redirected to "user/9622/dashboard"
    Then I click "Private Community Created by Supper Admin"
    Then I should be redirected to "groups/community/private-community-created-supper-admin"
    Then I should see "Private Community Created by Supper Admin"
    Then I click "Private Document Cre..."
    Then I should be redirected to "groups/document/private-community-created-supper-admin/private-document-created-supper-admin"
    Then I click "Private Community Created by Supper Admin"
    Then I should be redirected to "groups/community/private-community-created-supper-admin"
    Then I click "Archives"
    Then I should be redirected to "archives/10184"
    # code to redirect to a content which would be private and not associated with user's community.
    Given I am on "groups/document/api-working-group/open-data-recruitment-message-draft-08142013docx"
    Then I should see "You are not authorized to access this page."
    # need to change

