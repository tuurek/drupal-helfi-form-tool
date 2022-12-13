<?php

namespace Drupal\form_tool_contact_info\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a 'form_tool_contact_info'.
 *
 * Webform composites contain a group of sub-elements.
 *
 *
 * IMPORTANT:
 * Webform composite can not contain multiple value elements (i.e. checkboxes)
 * or composites (i.e. webform_address)
 *
 * @FormElement("form_tool_contact_info")
 *
 * @see \Drupal\webform\Element\WebformCompositeBase
 * @see \Drupal\form_tool_contact_info\Element\FormToolContactInfo
 */
class FormToolContactInfo extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $info = parent::getInfo();
    $class = get_class($this);
    $info['#pre_render'] = [
      [$class, 'preRenderCompositeFormElement'],
    ];
    $info['#theme'] = 'form_tool_contact_info';
    return parent::getInfo() + $info;
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [];
    $elements['Info'] = [
      '#type' => 'item',
      '#theme' => 'notification_content',
      '#notice_value' => t('Selecting a delivery method may prompt further questions'),
    ];
    $elements['Toimitustapa: Email'] = [
      '#type' => 'checkbox',
      '#title' => t('Email'),
      '#title_display' => 'before',
    ];
    $elements['Toimitustapa: Postitoimitus'] = [
      '#type' => 'checkbox',
      '#title' => t('Postal Delivery'),
      '#title_display' => 'before',
    ];
    $elements['Toimitustapa: Postiennakko'] = [
      '#type' => 'checkbox',
      '#title' => t('Cash on Delivery'),
      '#title_display' => 'before',
    ];
    $elements['Toimitustapa: Nouto'] = [
      '#type' => 'checkbox',
      '#title' => t('Pickup'),
      '#title_display' => 'before',
    ];
    $elements['delivery_method'] = [
      '#type' => 'radios',
      '#title' => t('Delivery'),
      '#title_display' => 'before',
      '#options' => [
        'email' => t('Email'),
        'postal' => t('Postal Delivery'),
        'cod' => t('Cash on Delivery'),
        'pickup' => t('Pick Up'),
      ],
      '#required' => TRUE,
      '#after_build' => [[get_called_class(), 'deliveryOptions']],
    ];
    $elements['first_name'] = [
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#after_build' => [[get_called_class(), 'postalAddress']],
    ];
    $elements['last_name'] = [
      '#type' => 'textfield',
      '#title' => t('Last name'),
      '#after_build' => [[get_called_class(), 'postalAddress']],
    ];
    $elements['street_address'] = [
      '#type' => 'textfield',
      '#title' => t('Street Address'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'postalAddress']],
    ];
    $elements['zip_code'] = [
      '#type' => 'textfield',
      '#title' => t('Zip Code'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'postalAddress']],
    ];
    $elements['city'] = [
      '#type' => 'textfield',
      '#title' => t('City'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'postalAddress']],
    ];
    $elements['phone_number'] = [
      '#type' => 'textfield',
      '#title' => t('Phone Number'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'postalAddress']],
    ];
    $elements['cod'] = [
      '#type' => 'item',
      '#markup' => t('Cash on delviery price is 9,20 €'),
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['cod_first_name'] = [
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['cod_last_name'] = [
      '#type' => 'textfield',
      '#title' => t('Last name'),
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['cod_street_address'] = [
      '#type' => 'textfield',
      '#title' => t('Street Address'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['cod_zip_code'] = [
      '#type' => 'textfield',
      '#title' => t('Zip Code'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['cod_city'] = [
      '#type' => 'textfield',
      '#title' => t('City'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['cod_phone_number'] = [
      '#type' => 'textfield',
      '#title' => t('Phone Number'),
      // Use #after_build to add #states.
      '#after_build' => [[get_called_class(), 'codPostalAddress']],
    ];
    $elements['pickup'] = [
      '#type' => 'item',
      '#markup' => 'Noudetaan kasvatuksen ja koulutuksen toimialan arkistolta. Töysänkatu 2 D, 00510 Helsinki.',
      '#after_build' => [[get_called_class(), 'pickup']],
    ];
    $elements['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email'),
      '#after_build' => [[get_called_class(), 'email']],
    ];
    $elements['Postiennakko -teksti'] = [
      '#type' => 'item',
      '#title' => t('Cash on delviery price is 9,20 €'),
    ];
    $elements['Nouto -teksti'] = [
      '#type' => 'textfield',
      '#title' => t('Pick-up from Töysänkatu 2 D, 00510 Helsinki.'),
    ];

    return $elements;
  }

  /**
   * Performs the after_build callback.
   */
  public static function deliveryOptions(array $element, FormStateInterface $form_state) {
    return $element;
  }

  /**
   * Performs the after_build callback.
   */
  public static function email(array $element, FormStateInterface $form_state) {
    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['visible'] = [
      [':input[name="' . $composite_name . '[delivery_method]"]' => ['value' => 'email']],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

  /**
   * Performs the after_build callback.
   */
  public static function postalAddress(array $element, FormStateInterface $form_state) {
    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['visible'] = [
      [':input[name="' . $composite_name . '[delivery_method]"]' => ['value' => 'postal']],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

  /**
   * Performs the after_build callback.
   */
  public static function codPostalAddress(array $element, FormStateInterface $form_state) {
    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['visible'] = [
      [':input[name="' . $composite_name . '[delivery_method]"]' => ['value' => 'cod']],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

  /**
   * Performs the after_build callback.
   */
  public static function pickup(array $element, FormStateInterface $form_state) {
    // Add #states targeting the specific element and table row.
    preg_match('/^(.+)\[[^]]+]$/', $element['#name'], $match);
    $composite_name = $match[1];
    $element['#states']['visible'] = [
      [':input[name="' . $composite_name . '[delivery_method]"]' => ['value' => 'pickup']],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

  /**
   * Performs the after_build callback.
   */
  public static function preRenderWebformCompositeFormElement($element) {
    $element = parent::preRenderWebformCompositeFormElement($element);

    if ($element['Toimitustapa: Email']['#access'] != 1) {
      unset($element['delivery_method']['email']);
    }
    else {
      $element['delivery_method']['email']['#title'] = $element['Toimitustapa: Email']['#title'];
    }
    unset($element['Toimitustapa: Email']);
    if ($element['Toimitustapa: Postitoimitus']['#access'] != 1) {
      unset($element['delivery_method']['postal']);
    }
    else {
      $element['delivery_method']['postal']['#title'] = $element['Toimitustapa: Postitoimitus']['#title'];
    }
    unset($element['Toimitustapa: Postitoimitus']);
    if ($element['Toimitustapa: Postiennakko']['#access'] != 1) {
      unset($element['delivery_method']['cod']);
    }
    else {
      $element['delivery_method']['cod']['#title'] = $element['Toimitustapa: Postiennakko']['#title'];
    }
    unset($element['Toimitustapa: Postiennakko']);
    if ($element['Toimitustapa: Nouto']['#access'] != 1) {
      unset($element['delivery_method']['pickup']);
    }
    else {
      $element['delivery_method']['pickup']['#title'] = $element['Toimitustapa: Nouto']['#title'];
    }
    $element['delivery_method']['#title'] = $element['#title'];
    unset($element['Toimitustapa: Nouto']);
    if ($element['Nouto -teksti']['#title'] != '') {
      $element['pickup']['#markup'] = $element['Nouto -teksti']['#title'];
    }
    unset($element['Nouto -teksti']);
    if ($element['Postiennakko -teksti']['#title'] != '') {
      $element['cod']['#markup'] = $element['Postiennakko -teksti']['#title'];
    }
    unset($element['Postiennakko -teksti']);

    $elements['Toimitustapa: Email'] = [
      '#type' => 'checkbox',
      '#title' => t('Email'),
      '#title_display' => 'before',
    ];
    $elements['Toimitustapa: Postitoimitus'] = [
      '#type' => 'checkbox',
      '#title' => t('Postal Delivery'),
      '#title_display' => 'before',
    ];
    $elements['Toimitustapa: Postiennakko'] = [
      '#type' => 'checkbox',
      '#title' => t('Cash on Delivery'),
      '#title_display' => 'before',
    ];
    $elements['Toimitustapa: Nouto'] = [
      '#type' => 'checkbox',
      '#title' => t('Pickup'),
      '#title_display' => 'before',
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function processWebformComposite(&$element, FormStateInterface $form_state, &$complete_form): array {
    // Process element.
    $element = parent::processWebformComposite($element, $form_state, $complete_form);

    // Load submission & data.
    $submission = $form_state->getFormObject()->getEntity();
    $submissionData = $submission->getData();

    // Loop data & make sure it's set properly with #default_values
    // process only 2 levels,.
    // @todo check if more dynamic parsing is necessary with address forms.
    foreach ($submissionData as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $key2 => $value2) {
          if (!is_array($value2)) {
            if (isset($element[$key2])) {
              $element[$key2]['#default_value'] = $value2;
            }
          }
        }
      }
      else {
        $element[$key]['#default_value'] = $value;
      }
    }

    return $element;
  }

}
