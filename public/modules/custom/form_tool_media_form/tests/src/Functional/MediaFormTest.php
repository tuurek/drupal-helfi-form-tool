<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Functional;

use Drupal\Core\Url;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests media form functionality.
 *
 * @group form_tool_media_form
 */
class MediaFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'link',
    'form_tool_media_form',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    // Setup standalone media urls from the settings.
    $this->config('media.settings')->set('standalone_url', TRUE)
      ->save();
    $this->refreshVariables();
    // Rebuild routes.
    \Drupal::service('router.builder')->rebuild();

    $account = $this->createUser([
      'view media',
      'create media',
      'update media',
      'update any media',
      'delete media',
      'delete any media',
    ]);
    $this->drupalLogin($account);
  }

  /**
   * Asserts media form formatter.
   *
   * @var int $media_id$
   *   The media id.
   */
  private function assertFormFormatter(int $media_id) : void {
    $media = Media::load($media_id);

    $this->drupalGet(Url::fromRoute('entity.media.revision', [
      'media' => $media->id(),
      'media_revision' => $media->getRevisionId(),
    ]));
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests 'hel_remote_webform' media type.
   */
  public function testMediaType() : void {
    \Drupal::service('entity_display.repository')->getViewDisplay('media', MediaType::load('hel_remote_webform')->id(), 'full')
      ->setComponent('field_media_hel_remote_webform', [
        'type' => 'hel_media_form',
      ])
      ->save();

    $this->drupalGet(Url::fromRoute('entity.media.add_form', ['media_type' => 'hel_remote_webform']));
    $this->assertSession()->statusCodeEquals(200);

    $this->submitForm([
      'name[0][value]' => 'Test value',
      'field_media_hel_remote_webform[0][uri]' => 'https://google.com',
    ], 'Save');

    // Make sure we only allow valid domains.
    $this->assertSession()->pageTextContainsOnce('Given host (google.com) is not valid, must be one of: nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi, hel-fi-form-tool.docker.so');

    // Make sure we can add valid forms.
    $urls = [
      'https://hel-fi-form-tool.docker.so/link/9UC458',
      'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/address/helsinki/Keskuskatu/8?city=helsinki,espoo,vantaa,kauniainen',
    ];

    foreach ($urls as $delta => $url) {
      $this->drupalGet(Url::fromRoute('entity.media.add_form', ['media_type' => 'hel_remote_webform']));
      $this->assertSession()->statusCodeEquals(200);

      $this->submitForm([
        'name[0][value]' => 'Form value ' . $delta,
        'field_media_hel_remote_webform[0][uri]' => $url,
      ], 'Save');
      $this->assertSession()->pageTextContainsOnce("Form (hel-fi-form-tool.docker.so, nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi) Form value $delta has been created.");

      $medias = \Drupal::entityTypeManager()->getStorage('media')->loadByProperties([
        'name' => 'Form value ' . $delta,
      ]);
      /** @var \Drupal\media\MediaInterface */
      $media = reset($medias);
      $this->drupalGet(Url::fromRoute('entity.media.canonical', ['media' => $media->id()])->toString());
      $this->assertSession()->statusCodeEquals(200);
    }
  }

}
