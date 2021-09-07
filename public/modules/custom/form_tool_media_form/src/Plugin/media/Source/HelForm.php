<?php

declare(strict_types = 1);

namespace Drupal\form_tool_media_form\Plugin\media\Source;

use Drupal\media\MediaSourceBase;
use Drupal\media\MediaTypeInterface;

/**
 * Form entity media source.
 *
 * @MediaSource(
 *   id = "hel_remote_webform",
 *   label = @Translation("Form - nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi and hel-fi-form-tool.docker.so"),
 *   allowed_field_types = {"string"},
 *   description = @Translation("Provides business logic and metadata for Helsinki forms."),
 *   forms = {
 *     "media_library_add" = "Drupal\form_tool_media_form\Form\FormToolMediaFormAddForm"
 *   }
 * )
 */
final class HelForm extends MediaSourceBase {

  public const PALVELUFORM_URL = 'nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi';
  public const FORM_URL = 'hel-fi-form-tool.docker.so';

  /**
   * List of valid form base urls.
   */
  public const VALID_URLS = [
    'palvelukartta' => self::PALVELUFORM_URL,
    'kartta' => self::FORM_URL,
  ];

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() : array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function createSourceField(MediaTypeInterface $type) {
    $storage = $this->getSourceFieldStorage() ?: $this->createSourceFieldStorage();
    return $this->entityTypeManager
      ->getStorage('field_config')
      ->create([
        'field_storage' => $storage,
        'bundle' => $type->id(),
        'label' => '&lt;Script/&gt;-tag from Lomaketyökalu',
        'required' => TRUE,
      ]);
  }

}
