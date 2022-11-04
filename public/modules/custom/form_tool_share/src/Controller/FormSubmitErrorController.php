<?php

namespace Drupal\form_tool_share\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Form Tool Share routes.
 */
class FormSubmitErrorController extends ControllerBase {

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
   * @return array
   *   Render array.
   */
  public function build(): array {

    $msg = $this->t('Form submission failed, please contact support');

    return [
      '#theme' => 'form_tool_share_completion',
      '#submissionId' => NULL,
      '#submissionData' => NULL,
      '#message' => $msg,
    ];
  }

}
