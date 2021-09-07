<?php

declare(strict_types = 1);

namespace Drupal\form_tool_media_form\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\form_tool_media_form\UrlParserTrait;
use League\Uri\Http;
use Drupal\Component\Utility\Html;

/**
 * Plugin implementation of the 'Form' formatter.
 *
 * @FieldFormatter(
 *   id = "hel_remote_webform",
 *   label = @Translation("Form"),
 *   field_types = {
 *     "string",
 *   }
 * )
 */
final class MediaFormFormatter extends FormatterBase {

  use UrlParserTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'link_title' => 'Open form in new window',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) : array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['link_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link title'),
      '#default_value' => $this->getSetting('link_title'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $html = $item->getString();
      foreach (Html::load($html)->getElementsByTagName('script') as $script) {
        $uri = $script->getAttribute('src');
        $uri = Http::createFromString($uri);
      }

      $elements[$delta] = [
        '#theme' => 'form_tool_embedded',
        '#url' => $uri,
      ];
    }

    return $elements;
  }

}
