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
   * @param string $submissionId
   *   Form / submission id.
   *
   * @return array
   *   Render array.
   */
  public function build(string $submissionId): array {
    /** @var \Drupal\webform\Entity\WebformSubmission $entity */
    $entity = FormToolWebformHandler::submissionObjectAndDataFromFormId($submissionId);

    $url = Url::fromRoute(
      'webform_formtool_handler.view_submission',
      ['id' => $submissionId],
      [
        'attributes' => [
          'data-drupal-selector' => 'form-submitted-ok',
          'target' => '_blank',
        ],
      ]
    );

    $t_args = [
      '@number' => $submissionId,
      '@link' => Link::fromTextAndUrl('here', $url)->toString(),
    ];

    $msg = $this->t(
      'Form submission (@number) saved, see submitted data from @link',
      $t_args
    );

    return [
      '#theme' => 'form_tool_share_completion',
      '#submissionId' => $submissionId,
      '#submissionData' => $entity->getData(),
      '#message' => $msg,
    ];
  }

}
