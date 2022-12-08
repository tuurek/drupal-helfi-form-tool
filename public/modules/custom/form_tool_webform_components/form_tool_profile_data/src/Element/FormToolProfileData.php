<?php

namespace Drupal\form_tool_profile_data\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Element\WebformCompositeBase;
use Drupal\form_tool_profile_data\Plugin\WebformElement\FormToolProfileData as ProfileDataElement;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

    // If user is not helsinkiproifile user we don't have any user info.
    $currentUserRoles = \Drupal::currentUser()->getRoles();
    if (
      !in_array('helsinkiprofiili_vahva', $currentUserRoles) &&
      !in_array('helsinkiprofiili_heikko', $currentUserRoles)) {
      return [];
    }

    $options = ProfileDataElement::getFieldSelections();

    $userProfile = $hpud->getUserProfileData();

    if (!\Drupal::service('router.admin_context')->isAdminRoute()) {
      if ($userProfile == NULL || empty($userProfile) || empty($userProfile['myProfile'])) {
        throw new AccessDeniedHttpException('No profile data available');
      }
    }

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
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["firstName"],
          ],
          '#required' => TRUE,
        ];
        $elements['verifiedFirstName']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
      }
      if (isset($selectedFields['verifiedLastName']) && $selectedFields['verifiedLastName'] !== 0) {
        $elements['verifiedLastName'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedLastName'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["lastName"],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["lastName"],
          ],
          '#required' => TRUE,
        ];
        $elements['verifiedLastName']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
      }
      if (isset($selectedFields['verifiedSsn']) && $selectedFields['verifiedSsn'] !== 0) {
        $elements['verifiedSsn'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedSsn'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["nationalIdentificationNumber"],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["nationalIdentificationNumber"],
          ],
          '#required' => TRUE,
        ];
        $elements['verifiedSsn']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';

      }
      if (isset($selectedFields['verifiedGivenName']) && $selectedFields['verifiedGivenName'] !== 0) {
        $elements['verifiedGivenName'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedGivenName'],
          '#value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["givenName"],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => $userProfile["myProfile"]["verifiedPersonalInformation"]["givenName"],
          ],
          '#required' => TRUE,
        ];
        $elements['verifiedGivenName']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
      }
      if (isset($selectedFields['verifiedPermanentAddress']) && $selectedFields['verifiedPermanentAddress'] !== 0) {
        $permanent_address = [
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["streetAddress"],
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["postalCode"],
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["postOffice"],
        ];
        $elements['verifiedPermanentAddress'] = [
          '#type' => 'textfield',
          '#title' => $options['strong']['verifiedPermanentAddress'],
          '#value' =>
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["streetAddress"] . ', ' .
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["postalCode"] . ', ' .
          $userProfile["myProfile"]["verifiedPersonalInformation"]["permanentAddress"]["postOffice"],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => implode(', ', $permanent_address),
          ],
          '#required' => TRUE,
        ];
        $elements['verifiedPermanentAddress']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
      }
    }

    if (isset($element['#weak'])) {
      $selectedFields = $element['#weak'];

      if (isset($selectedFields['primaryAddress']) && $selectedFields['primaryAddress'] !== 0) {
        $primary_address = [
          $userProfile["myProfile"]["primaryAddress"]["address"],
          $userProfile["myProfile"]["primaryAddress"]["postalCode"],
          $userProfile["myProfile"]["primaryAddress"]["city"],
          $userProfile["myProfile"]["primaryAddress"]["countryCode"],
        ];
        $elements['primaryAddress'] = [
          '#type' => 'textfield',
          '#title' => $options['weak']['primaryAddress'],
          '#value' =>
          $userProfile["myProfile"]["primaryAddress"]["address"] . ', ' .
          $userProfile["myProfile"]["primaryAddress"]["postalCode"] . ', ' .
          $userProfile["myProfile"]["primaryAddress"]["city"] . ', ' .
          $userProfile["myProfile"]["primaryAddress"]["countryCode"],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => implode(', ', $primary_address),
          ],
          '#required' => TRUE,
        ];
        $elements['primaryAddress']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
      }
      if (isset($selectedFields['primaryEmail']) && $selectedFields['primaryEmail'] !== 0) {
        $elements['primaryEmail'] = [
          '#type' => 'textfield',
          '#title' => $options['weak']['primaryEmail'],
          '#value' => $userProfile["myProfile"]["primaryEmail"]["email"],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => $userProfile["myProfile"]["primaryEmail"]["email"],
          ],
          '#required' => TRUE,
        ];
        $elements['primaryEmail']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
      }
      if (isset($selectedFields['primaryPhone']) && $selectedFields['primaryPhone'] !== 0) {
        $elements['primaryPhone'] = [
          '#type' => 'textfield',
          '#title' => $options['weak']['primaryPhone'],
          '#value' => $userProfile["myProfile"]["primaryPhone"]["phone"],
          '#description' => [
            '#theme' => 'profile_data_icon',
            '#text_value' => $userProfile["myProfile"]["primaryPhone"]["phone"],
          ],
          '#attributes' => ['readonly' => 'readonly', 'style' => 'display:none'],
          '#required' => TRUE,
        ];
        $elements['primaryPhone']['#wrapper_attributes']['class'][] = 'form_tool__prefilled_field';
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
