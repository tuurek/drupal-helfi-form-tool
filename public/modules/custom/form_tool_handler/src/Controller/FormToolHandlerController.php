<?php

namespace Drupal\form_tool_handler\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for form_tool_handler routes.
 */
class FormToolHandlerController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
