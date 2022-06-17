<?php

namespace Drupal\webform_formtool_handler\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Drupal\webform_formtool_handler\Plugin\WebformHandler\FormToolWebformHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Form Tool Handler routes.
 */
class FormToolSubmissionController extends ControllerBase {

  /**
   * The helfi_atv service.
   *
   * @var \Drupal\helfi_atv\AtvService
   */
  protected AtvService $helfiAtv;

  /**
   * The helfi_helsinki_profiili service.
   *
   * @var \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData
   */
  protected HelsinkiProfiiliUserData $helfiHelsinkiProfiili;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $account;

  /**
   * The controller constructor.
   *
   * @param \Drupal\helfi_atv\AtvService $helfi_atv
   *   The helfi_atv service.
   * @param \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData $helfi_helsinki_profiili
   *   The helfi_helsinki_profiili service.
   */
  public function __construct(
    AtvService $helfi_atv,
    HelsinkiProfiiliUserData $helfi_helsinki_profiili
  ) {
    $this->helfiAtv = $helfi_atv;
    $this->helfiHelsinkiProfiili = $helfi_helsinki_profiili;
    $this->account = \Drupal::currentUser();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('helfi_atv.atv_service'),
      $container->get('helfi_helsinki_profiili.userdata')
    );
  }

  /**
   * Loads webform submission by the human readable format (HEL-XXX-XXX).
   *
   * Also makes access checks and returns data to be handled in template.
   *
   * Not that in this we should only parse form data itself, and add other
   * webform data only if needed.
   *
   * @param string $id
   *   ID of the submission. Human readable format.
   *
   * @return array
   *   Render array for template.
   */
  public function build(string $id): array {

    $entity = FormToolWebformHandler::submissionObjectAndDataFromFormId($id);

    // dump($entity);
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('webform_submission');
    $pre_render = $view_builder->view($entity);

    $formTitle = $entity->getWebform()->get('title');

    return [
      '#theme' => 'submission_print',
      '#id' => $id,
      '#submission' => $pre_render,
      '#form' => $formTitle,
    ];
  }

}
