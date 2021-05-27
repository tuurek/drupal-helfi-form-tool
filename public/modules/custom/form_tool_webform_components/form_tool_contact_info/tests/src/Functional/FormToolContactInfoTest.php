<?php

namespace Drupal\Tests\form_tool_contact_info\Functional;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\Tests\webform\Functional\WebformBrowserTestBase;

/**
 * Tests for webform example composite.
 *
 * @group form_tool_contact_info
 */
class FormToolContactInfoTest extends WebformBrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['form_tool_contact_info'];

  /**
   * Tests webform example element.
   */
  public function testFormToolContactInfo() {
    $webform = Webform::load('form_tool_contact_info');

    // Check form element rendering.
    $this->drupalGet('/webform/form_tool_contact_info');
    // NOTE:
    // This is a very lazy but easy way to check that the element is rendering
    // as expected.
    $this->assertRaw('<label for="edit-webform-example-composite-first-name">First name</label>');
    $this->assertFieldById('edit-webform-example-composite-first-name');
    $this->assertRaw('<label for="edit-webform-example-composite-last-name">Last name</label>');
    $this->assertFieldById('edit-webform-example-composite-last-name');
    $this->assertRaw('<label for="edit-webform-example-composite-date-of-birth">Date of birth</label>');
    $this->assertFieldById('edit-webform-example-composite-date-of-birth');
    $this->assertRaw('<label for="edit-webform-example-composite-sex">Sex</label>');
    $this->assertFieldById('edit-webform-example-composite-sex');

    // Check webform element submission.
    $edit = [
      'form_tool_contact_info[first_name]' => 'John',
      'form_tool_contact_info[last_name]' => 'Smith',
      'form_tool_contact_info[sex]' => 'Male',
      'form_tool_contact_info[date_of_birth]' => '1910-01-01',
      'form_tool_contact_info_multiple[items][0][first_name]' => 'Jane',
      'form_tool_contact_info_multiple[items][0][last_name]' => 'Doe',
      'form_tool_contact_info_multiple[items][0][sex]' => 'Female',
      'form_tool_contact_info_multiple[items][0][date_of_birth]' => '1920-12-01',
    ];
    $sid = $this->postSubmission($webform, $edit);
    $webform_submission = WebformSubmission::load($sid);
    $this->assertEqual($webform_submission->getElementData('form_tool_contact_info'), [
      'first_name' => 'John',
      'last_name' => 'Smith',
      'sex' => 'Male',
      'date_of_birth' => '1910-01-01',
    ]);
    $this->assertEqual($webform_submission->getElementData('form_tool_contact_info_multiple'), [
      [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'sex' => 'Female',
        'date_of_birth' => '1920-12-01',
      ],
    ]);
  }

}
