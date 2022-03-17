<?php

namespace Drupal\form_tool_contact_info\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElement\WebformCompositeBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Provides a 'form_tool_contact_info' element.
 *
 * @WebformElement(
 *   id = "form_tool_contact_info",
 *   label = @Translation("Delivery Method"),
 *   description = @Translation("Delivery Method component."),
 *   category = @Translation("Composite elements"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = TRUE,
 * )
 *
 * @see \Drupal\form_tool_contact_info\Element\FormToolContactInfo
 * @see \Drupal\webform\Plugin\WebformElement\WebformCompositeBase
 * @see \Drupal\webform\Plugin\WebformElementBase
 * @see \Drupal\webform\Plugin\WebformElementInterface
 * @see \Drupal\webform\Annotation\WebformElement
 */
class FormToolContactInfo extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return $this->formatTextItemValue($element, $webform_submission, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function defineDefaultProperties() {
    // Here you define your webform element's default properties,
    // which can be inherited.
    //
    // @see \Drupal\webform\Plugin\WebformElementBase::defaultProperties
    // @see \Drupal\webform\Plugin\WebformElementBase::defaultBaseProperties
    $properties = parent::defineDefaultProperties();

    unset($properties['format_attributes']);
    unset($properties['required']);
    unset($properties['required_container']);
    unset($properties['custom']);
    unset($properties['properties']);
    unset($properties['admin']);
    unset($properties['private']);
    unset($properties['admin_title']);
    unset($properties['admin_notes']);
    unset($properties['access_create']);
    unset($properties['access_create_roles']);
    unset($properties['access_create_users']);
    unset($properties['access_create_permissions']);
    unset($properties['access_update']);
    unset($properties['access_update_roles']);
    unset($properties['access_update_users']);
    unset($properties['access_update_permissions']);
    unset($properties['access_view']);
    unset($properties['access_view_roles']);
    unset($properties['access_view_users']);
    unset($properties['access_view_permissions']);
    unset($properties['access']);
    unset($properties['item']);
    unset($properties['display']);
    unset($properties['format']);
    unset($properties['format_html']);
    unset($properties['format_text']);
    unset($properties['description']);
    unset($properties['format_items']);
    unset($properties['format_items_html']);
    unset($properties['format_items_text']);
    unset($properties['wrapper_type']);
    unset($properties['wrapper_attributes']);
    unset($properties['element_attributes']);
    unset($properties['attributes']);
    unset($properties['label_attributes']);
    unset($properties['conditional_logic']);
    unset($properties['states']);
    unset($properties['states_clear']);
    unset($properties['states_required_message']);
    unset($properties['default']);
    unset($properties['default_value']);
    unset($properties['set_default_value']);
    unset($properties['description']);
    unset($properties['element_description']);
    unset($properties['help_title']);
    unset($properties['help']);
    unset($properties['more_title']);
    unset($properties['more']);
    unset($properties['disabled']);
    unset($properties['prepopulate']);
    unset($properties['description_display']);
    unset($properties['help_display']);
    unset($properties['field_container']);
    unset($properties['field_prefix']);
    unset($properties['field_suffix']);
    unset($properties['trim']);
    unset($properties['select2']);
    unset($properties['flexbox']);
    unset($properties['title_display']);
    unset($properties['settings']);
    unset($properties['delivery_method']);
    unset($properties['delivery_method__title_display']);
    unset($properties['delivery_method__key']);
    unset($properties['delivery_method__options']);
    unset($properties['delivery_method__title']);
    unset($properties['delivery_method__type']);
    unset($properties['delivery_method__description']);
    unset($properties['delivery_method__help']);
    unset($properties['delivery_method__required']);
    unset($properties['delivery_method__placeholder']);
    unset($properties['delivery_method__access']);
    unset($properties['first_name__type']);
    unset($properties['first_name__title']);
    unset($properties['first_name__title_display']);
    unset($properties['first_name__description']);
    unset($properties['first_name__help']);
    unset($properties['first_name__required']);
    unset($properties['first_name__placeholder']);
    unset($properties['first_name__access']);
    unset($properties['last_name__type']);
    unset($properties['last_name__title']);
    unset($properties['last_name__title_display']);
    unset($properties['last_name__description']);
    unset($properties['last_name__help']);
    unset($properties['last_name__required']);
    unset($properties['last_name__placeholder']);
    unset($properties['last_name__access']);
    unset($properties['street_address__type']);
    unset($properties['street_address__title']);
    unset($properties['street_address__title_display']);
    unset($properties['street_address__description']);
    unset($properties['street_address__help']);
    unset($properties['street_address__required']);
    unset($properties['street_address__placeholder']);
    unset($properties['street_address__access']);
    unset($properties['zip_code__type']);
    unset($properties['zip_code__title']);
    unset($properties['zip_code__title_display']);
    unset($properties['zip_code__description']);
    unset($properties['zip_code__help']);
    unset($properties['zip_code__required']);
    unset($properties['zip_code__placeholder']);
    unset($properties['zip_code__access']);
    unset($properties['city__type']);
    unset($properties['city__title']);
    unset($properties['city__title_display']);
    unset($properties['city__description']);
    unset($properties['city__help']);
    unset($properties['city__required']);
    unset($properties['city__placeholder']);
    unset($properties['city__access']);
    unset($properties['phone_number__type']);
    unset($properties['phone_number__title']);
    unset($properties['phone_number__title_display']);
    unset($properties['phone_number__description']);
    unset($properties['phone_number__help']);
    unset($properties['phone_number__required']);
    unset($properties['phone_number__placeholder']);
    unset($properties['phone_number__access']);
    unset($properties['cod_first_name__type']);
    unset($properties['cod_first_name__title']);
    unset($properties['cod_first_name__title_display']);
    unset($properties['cod_first_name__description']);
    unset($properties['cod_first_name__help']);
    unset($properties['cod_first_name__required']);
    unset($properties['cod_first_name__placeholder']);
    unset($properties['cod_first_name__access']);
    unset($properties['cod_last_name__type']);
    unset($properties['cod_last_name__title']);
    unset($properties['cod_last_name__title_display']);
    unset($properties['cod_last_name__description']);
    unset($properties['cod_last_name__help']);
    unset($properties['cod_last_name__required']);
    unset($properties['cod_last_name__placeholder']);
    unset($properties['cod_last_name__access']);
    unset($properties['cod_street_address__type']);
    unset($properties['cod_street_address__title']);
    unset($properties['cod_street_address__title_display']);
    unset($properties['cod_street_address__description']);
    unset($properties['cod_street_address__help']);
    unset($properties['cod_street_address__required']);
    unset($properties['cod_street_address__placeholder']);
    unset($properties['cod_street_address__access']);
    unset($properties['cod_zip_code__type']);
    unset($properties['cod_zip_code__title']);
    unset($properties['cod_zip_code__title_display']);
    unset($properties['cod_zip_code__description']);
    unset($properties['cod_zip_code__help']);
    unset($properties['cod_zip_code__required']);
    unset($properties['cod_zip_code__placeholder']);
    unset($properties['cod_zip_code__access']);
    unset($properties['cod_city__type']);
    unset($properties['cod_city__title']);
    unset($properties['cod_city__title_display']);
    unset($properties['cod_city__description']);
    unset($properties['cod_city__help']);
    unset($properties['cod_city__required']);
    unset($properties['cod_city__placeholder']);
    unset($properties['cod_city__access']);
    unset($properties['cod_phone_number__type']);
    unset($properties['cod_phone_number__title']);
    unset($properties['cod_phone_number__title_display']);
    unset($properties['cod_phone_number__description']);
    unset($properties['cod_phone_number__help']);
    unset($properties['cod_phone_number__required']);
    unset($properties['cod_phone_number__placeholder']);
    unset($properties['cod_phone_number__access']);
    unset($properties['cod__type']);
    unset($properties['cod__title']);
    unset($properties['cod__title_display']);
    unset($properties['cod__description']);
    unset($properties['cod__help']);
    unset($properties['cod__required']);
    unset($properties['cod__placeholder']);
    unset($properties['cod__access']);
    unset($properties['email__type']);
    unset($properties['email__title']);
    unset($properties['email__title_display']);
    unset($properties['email__description']);
    unset($properties['email__help']);
    unset($properties['email__required']);
    unset($properties['email__placeholder']);
    unset($properties['email__access']);
    unset($properties['hidden_pickup__type']);
    unset($properties['hidden_pickup__title_display']);
    unset($properties['hidden_pickup__description']);
    unset($properties['hidden_pickup__help']);
    unset($properties['hidden_pickup__required']);
    unset($properties['hidden_pickup__placeholder']);
    unset($properties['hidden_pickup__access']);
    unset($properties['pickup__type']);
    unset($properties['pickup__title_display']);
    unset($properties['pickup__description']);
    unset($properties['pickup__help']);
    unset($properties['pickup__required']);
    unset($properties['pickup__placeholder']);
    unset($properties['pickup__access']);
    unset($properties['multiple']);
    unset($properties['multiple__header']);
    unset($properties['multiple__header_label']);
    unset($properties['multiple__item_label']);
    unset($properties['multiple__no_items_message']);
    unset($properties['multiple__min_items']);
    unset($properties['multiple__empty_items']);
    unset($properties['multiple__sorting']);
    unset($properties['multiple__operations']);
    unset($properties['multiple__add']);
    unset($properties['multiple__remove']);
    unset($properties['multiple__add_more']);
    unset($properties['multiple__add_more_container']);
    unset($properties['multiple__add_more_input']);
    unset($properties['multiple__add_more_button_label']);
    unset($properties['multiple__add_more_input_label']);
    unset($properties['multiple__add_more_items']);
    unset($properties['choices']);
    unset($properties['chosen']);
    unset($properties['flex']);
    unset($properties['Toimitustapa: Email__type']);
    unset($properties['Toimitustapa: Email__title_display']);
    unset($properties['Toimitustapa: Email__description']);
    unset($properties['Toimitustapa: Email__help']);
    unset($properties['Toimitustapa: Email__required']);
    unset($properties['Toimitustapa: Email__placeholder']);
    unset($properties['Toimitustapa: Postitoimitus__type']);
    unset($properties['Toimitustapa: Postitoimitus__title_display']);
    unset($properties['Toimitustapa: Postitoimitus__description']);
    unset($properties['Toimitustapa: Postitoimitus__help']);
    unset($properties['Toimitustapa: Postitoimitus__required']);
    unset($properties['Toimitustapa: Postitoimitus__placeholder']);
    unset($properties['Toimitustapa: Postiennakko__type']);
    unset($properties['Toimitustapa: Postiennakko__title_display']);
    unset($properties['Toimitustapa: Postiennakko__description']);
    unset($properties['Toimitustapa: Postiennakko__help']);
    unset($properties['Toimitustapa: Postiennakko__required']);
    unset($properties['Toimitustapa: Postiennakko__placeholder']);
    unset($properties['Toimitustapa: Nouto__type']);
    unset($properties['Toimitustapa: Nouto__title_display']);
    unset($properties['Toimitustapa: Nouto__description']);
    unset($properties['Toimitustapa: Nouto__help']);
    unset($properties['Toimitustapa: Nouto__required']);
    unset($properties['Toimitustapa: Nouto__placeholder']);
    unset($properties['Postiennakko -teksti__type']);
    unset($properties['Postiennakko -teksti__title_display']);
    unset($properties['Postiennakko -teksti__description']);
    unset($properties['Postiennakko -teksti__help']);
    unset($properties['Postiennakko -teksti__required']);
    unset($properties['Postiennakko -teksti__placeholder']);
    unset($properties['Postiennakko -teksti__access']);
    unset($properties['Nouto -teksti__type']);
    unset($properties['Nouto -teksti__title_display']);
    unset($properties['Nouto -teksti__description']);
    unset($properties['Nouto -teksti__help']);
    unset($properties['Nouto -teksti__required']);
    unset($properties['Nouto -teksti__placeholder']);
    unset($properties['Nouto -teksti__access']);
    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);

    $lines = [];

    switch ($value["delivery_method"]) {
      case 'email':
        $lines[] = $value['email'];
        break;

      case 'pickup':
        $tt = $this->t('Noudetaan kasvatuksen ja koulutuksen toimialan arkistolta. Töysänkatu 2 D, 00510 Helsinki.');
        $lines[] = $tt->render();
        break;

      case 'postal':
        $lines[] = $value['first_name'] . ' ' . $value['last_name'];
        break;

      case 'cod':
        $lines[] = $value['cod_first_name'] . ' ' . $value['cod_last_name'];
        $lines[] = $value['cod_street_address'] . ' ' . $value['cod_zip_code'] . ' ' . $value['cod_city'];
        $lines[] = $value['cod_phone_number'];
        break;

      default:
        $lines[] = ($value['first_name'] ? $value['first_name'] : '') .
          ($value['last_name'] ? ' ' . $value['last_name'] : '');
        break;

    }

    return $lines;
  }

}
