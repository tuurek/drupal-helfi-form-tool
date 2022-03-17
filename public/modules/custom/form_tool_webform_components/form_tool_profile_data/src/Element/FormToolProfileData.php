<?php

namespace Drupal\form_tool_profile_data\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Element\WebformCompositeBase;
use Drupal\form_tool_profile_data\Plugin\WebformElement\FormToolProfileData as ProfileDataElement;

/**
 * Provides a 'webform_example_composite'.
 *
 * Webform composites contain a group of sub-elements.
 *
 *
 * IMPORTANT:
 * Webform composite can not contain multiple value elements (i.e. checkboxes)
 * or composites (i.e. webform_address)
 *
 * @FormElement("form_tool_profile_data")
 *
 * @see \Drupal\webform\Element\WebformCompositeBase
 * @see \Drupal\form_tool_profile_data\Element\FormToolProfileData
 */
class FormToolProfileData extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo() + ['#theme' => 'form_tool_profile_data'];
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [];

    /** @var \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData $hpud */
    $hpud = \Drupal::service('helfi_helsinki_profiili.userdata');

    $options = ProfileDataElement::getFieldSelections();

    $userData = $hpud->getUserData();
    $userProfile = $hpud->getUserProfileData();

    if (isset($element['#strong'])) {

      $selectedFields = $element['#strong'];

      if ($userProfile === NULL) {
        return $elements;
      }

      if (isset($selectedFields['verifiedFirstName']) && $selectedFields['verifiedFirstName'] !== 0) {
        $elements['verifiedFirstName'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedFirstName'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["firstName"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
      if (isset($selectedFields['verifiedLastName']) && $selectedFields['verifiedLastName'] !== 0) {
        $elements['verifiedLastName'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedLastName'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["lastName"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
      if (isset($selectedFields['verifiedSsn']) && $selectedFields['verifiedSsn'] !== 0) {
        $elements['verifiedSsn'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedSsn'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["nationalIdentificationNumber"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
      if (isset($selectedFields['verifiedGivenName']) && $selectedFields['verifiedGivenName'] !== 0) {
        $elements['verifiedGivenName'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedGivenName'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["givenName"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
      if (isset($selectedFields['verifiedPermanentAddress']) && $selectedFields['verifiedPermanentAddress'] !== 0) {
        $elements['verifiedPermanentAddress'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedPermanentAddress'],
          '#value' =>
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["streetAddress"] . ', ' .
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["postalCode"] . ', ' .
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["postOffice"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
    }

    if (isset($element['#weak'])) {
      $selectedFields = $element['#weak'];

      if (isset($selectedFields['primaryAddress']) && $selectedFields['primaryAddress'] !== 0) {
        $elements['primaryAddress'] = [
          '#type' => 'textfield',
          '#title' => $options['weak']['primaryAddress'],
          '#value' =>
          $userProfile["myProfile"]["primaryAddress"]["address"] . ', ' .
          $userProfile["myProfile"]["primaryAddress"]["postalCode"] . ', ' .
          $userProfile["myProfile"]["primaryAddress"]["city"] . ', ' .
          $userProfile["myProfile"]["primaryAddress"]["countryCode"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
      if (isset($selectedFields['primaryEmail']) && $selectedFields['primaryEmail'] !== 0) {
        $elements['primaryEmail'] = [
          '#type' => 'textfield',
          '#title' => $options['weak']['primaryEmail'],
          '#value' => $userProfile["myProfile"]["primaryEmail"]["email"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
      if (isset($selectedFields['primaryPhone']) && $selectedFields['primaryPhone'] !== 0) {
        $elements['primaryPhone'] = [
          '#type' => 'textfield',
          '#title' => $options['weak']['primaryPhone'],
          '#value' => $userProfile["myProfile"]["primaryPhone"]["phone"],
          '#attributes' => ['readonly' => 'readonly'],
        ];
      }
    }

    return $elements;
  }

  /**
   * Performs the after_build callback.
   */
  public static function afterBuild(array $element, FormStateInterface $form_state) {
    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['disabled'] = [
      [':input[name="' . $composite_name . '[first_name]"]' => ['empty' => TRUE]],
      [':input[name="' . $composite_name . '[last_name]"]' => ['empty' => TRUE]],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

}
