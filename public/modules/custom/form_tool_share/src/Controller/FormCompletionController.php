<?php

namespace Drupal\form_tool_share\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\webform_formtool_handler\Plugin\WebformHandler\FormToolWebformHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Form Tool Share routes.
 */
class FormCompletionController extends ControllerBase {

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $account;

  /**
   * The controller constructor.
   */
  public function __construct() {
    $this->account = \Drupal::currentUser();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * Builds the response.
   *
   * @param string $submission_id
   *   Form / submission id.
   *
   * @return array
   *   Render array.
   */
  public function build(string $submission_id): array {
    /** @var \Drupal\webform\Entity\WebformSubmission $entity */
    $entity = FormToolWebformHandler::submissionObjectAndDataFromFormId($submission_id, 'view');

    $url = Url::fromRoute(
      'form_tool_share.view_submission',
      ['submission_id' => $submission_id],
      [
        'attributes' => [
          'data-drupal-selector' => 'form-submitted-ok',
          'target' => '_blank',
        ],
      ]
    );

    $t_args = [
      '@number' => $submission_id,
      '@link' => Link::fromTextAndUrl('here', $url)->toString(),
    ];

    $msg = $this->t(
      'Form submission (@number) saved, see submitted data from @link',
      $t_args
    );

    return [
      '#theme' => 'form_tool_share_completion',
      '#submissionId' => $submission_id,
      '#submissionData' => $entity->getData(),
      '#message' => $msg,
    ];
  }

}
