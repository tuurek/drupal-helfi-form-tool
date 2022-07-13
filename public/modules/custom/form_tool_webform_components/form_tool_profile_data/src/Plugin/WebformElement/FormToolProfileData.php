<?php

namespace Drupal\form_tool_profile_data\Plugin\WebformElement;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElement\WebformCompositeBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'form_tool_profile_data' element.
 *
 * @WebformElement(
 *   id = "form_tool_profile_data",
 *   label = @Translation("Form tool Profile data"),
 *   description = @Translation("Provides webform component to gather details from helsinki profile."),
 *   category = @Translation("Helfi"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 *
 * @see \Drupal\form_tool_profile_data\Element\WebformExampleComposite
 * @see \Drupal\webform\Plugin\WebformElement\WebformCompositeBase
 * @see \Drupal\webform\Plugin\WebformElementBase
 * @see \Drupal\webform\Plugin\WebformElementInterface
 * @see \Drupal\webform\Annotation\WebformElement
 */
class FormToolProfileData extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    // Here you define your webform element's default properties,
    // which can be inherited.
    //
    // @see \Drupal\webform\Plugin\WebformElementBase::defaultProperties
    // @see \Drupal\webform\Plugin\WebformElementBase::defaultBaseProperties
    return [
      'noauth' => [],
      'weak' => [],
      'strong' => [],
    ] + parent::defineDefaultProperties();
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {

    $form = parent::form($form, $form_state);

    $options = self::getFieldSelections();

    $webFormSettings = $this->getWebform()->getThirdPartySettings('form_tool_webform_parameters');

    // $form['element']['title']['#default_value'] = 'Profile fields';
    $form['element']['title']['#value'] = 'Profile fields';

    if (isset($webFormSettings['login_type']) && $webFormSettings['login_type'] === '0') {
      $form['element']['noauth'] = [
        '#type' => 'checkboxes',
        '#title' => t('Fields available with no auth'),
      ];
      $form['element']['noauth']['#options'] = $options['noauth'];
    }

    if (isset($webFormSettings['login_type']) && $webFormSettings['login_type'] === '1') {
      $form['element']['weak'] = [
        '#type' => 'checkboxes',
        '#title' => t('Fields available with weak auth'),
      ];
      $form['element']['weak']['#options'] = $options['weak'];
    }

    if (isset($webFormSettings['login_type']) && $webFormSettings['login_type'] === '2') {
      $form['element']['strong'] = [
        '#type' => 'checkboxes',
        '#title' => t('Fields available with strong auth'),
      ];
      $form['element']['strong']['#options'] = $options['strong'];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return $this->formatTextItemValue($element, $webform_submission, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);

    $titles = FormToolProfileData::getFieldSelections();

    $lines = [];
    foreach ($value as $fieldName => $fieldValue) {
      foreach ($titles as $auth => $fields) {
        if (isset($fields[$fieldName])) {
          $lines[] = $fields[$fieldName]->render() . ': ' . $fieldValue;
        }
      }

    }
    return $lines;
  }

  /**
   * Return fields from profile.
   *
   * @return array
   *   Fields for prefilled data.
   */
  public static function getFieldSelections(): array {
    return [
      'noauth' => [],
      'weak' => [
        'primaryAddress' => t('Primary address'),
        'primaryEmail' => t('Primary email'),
        'primaryPhone' => t('Primary phone'),
      ],
      'strong' => [
        'verifiedFirstName' => t('Verified first name'),
        'verifiedLastName' => t('Verified last name'),
        'verifiedGivenName' => t('Verified given name'),
        'verifiedSsn' => t('Verified SSN'),
        'verifiedPermanentAddress' => t('Verified permanent address'),
      ],
    ];
  }

}
