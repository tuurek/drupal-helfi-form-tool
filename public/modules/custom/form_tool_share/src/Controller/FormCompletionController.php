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

    // mitä pitää hakea tähän?
//    $submisisonObject = ASDFASASDF::getSubmissionsFor($submissionId);

    // - palvelusivun urli
    // - ite datan näyttämisen urli
    // - htmllää templateen
    // - translator?
    // - 3rd party wysiwyg




    return [
      '#theme' => 'form_tool_share_completion',
      '#submissionId' => $submissionId,
    ];
  }

}
