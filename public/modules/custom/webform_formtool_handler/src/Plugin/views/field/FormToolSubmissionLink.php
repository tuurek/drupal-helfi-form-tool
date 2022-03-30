<?php

namespace Drupal\webform_formtool_handler\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Provides WF Submission link field handler.
 *
 * @ViewsField("form_tool_submission_link")
 *
 * @DCG
 * The plugin needs to be assigned to a specific table column through
 * hook_views_data() or hook_views_data_alter().
 * For non-existent columns (i.e. computed fields) you need to override
 * self::query() method.
 */
class FormToolSubmissionLink extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {

    $entity = $values->_entity;

    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = \Drupal::service('database');

    $result = $connection->query("SELECT form_tool_id FROM {form_tool} WHERE submission_uuid = :submission_uuid", [
      ':submission_uuid' => $entity->uuid(),
    ]);
    $data = $result->fetchObject();

    if ($data == FALSE) {
      return '';
    }

    $linkUrl = '/lomake/nayta-tulokset/' . $data->form_tool_id;

    return [
      '#markup' => '<a href="' . $linkUrl . '" target="_blank">Avaa lähetys</a>',
    ];
  }

  /**
   * Empty function to disable data querying.
   */
  public function query() {
  }

}
