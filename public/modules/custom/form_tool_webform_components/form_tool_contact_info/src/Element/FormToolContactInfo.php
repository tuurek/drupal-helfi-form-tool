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
    return parent::getInfo() + ['#theme' => 'form_tool_contact_info'];
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [];
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
      '#markup' => 'Postiennakon hinta asiakirjan tilaajalle 9,20 €.',
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
    //$element['#markup'] = $form_state->getValue('hidden_pickup');

    $element['#states']['visible'] = [
      [':input[name="' . $composite_name . '[delivery_method]"]' => ['value' => 'pickup']],
    ];
    // Add .js-form-wrapper to wrapper (ie td) to prevent #states API from
    // disabling the entire table row when this element is disabled.
    $element['#wrapper_attributes']['class'][] = 'js-form-wrapper';
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    $properties = [
      // Element settings.
      'title' => '',
      'default_value' => [],
      // Description/Help.
      'help' => '',
      'help_title' => '',
      'description' => '',
      'more' => '',
      'more_title' => '',
      // Form display.
      'title_display' => 'invisible',
      'description_display' => '',
      'help_display' => '',
      // Form validation.
      'required' => FALSE,
      // Submission display.
      'format' => $this->getItemDefaultFormat(),
      'format_html' => '',
      'format_text' => '',
      'format_items' => $this->getItemsDefaultFormat(),
      'format_items_html' => '',
      'format_items_text' => '',
      // Address settings.
      'available_countries' => [],
      'field_overrides' => [],
      'langcode_override' => '',
    ] + $this->defineDefaultBaseProperties()
      + $this->defineDefaultMultipleProperties();
    unset($properties['multiple__header']);
    return $properties;
  }

}
