<?php

namespace Drupal\form_tool_share\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Form Tool Share routes.
 */
class FormCompletionController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build($submissionId): array {

    return [
      '#theme' => 'form_tool_share_completion',
      '#submissionId' => $submissionId,
    ];
  }

}
