<?php

namespace Drupal\form_tool_handler\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Form Tool Handler routes.
 */
class FormSubmissionController extends ControllerBase {

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
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * The controller constructor.
   *
   * @param \Drupal\helfi_atv\AtvService $helfi_atv
   *   The helfi_atv service.
   * @param \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData $helfi_helsinki_profiili
   *   The helfi_helsinki_profiili service.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection for fetching data.
   */
  public function __construct(
    AtvService $helfi_atv,
    HelsinkiProfiiliUserData $helfi_helsinki_profiili,
    Connection $connection
  ) {
    $this->helfiAtv = $helfi_atv;
    $this->helfiHelsinkiProfiili = $helfi_helsinki_profiili;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('helfi_atv.atv_service'),
      $container->get('helfi_helsinki_profiili.userdata'),
      $container->get('database')

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

    $result = $this->connection->query("SELECT submission_uuid FROM {form_tool} WHERE form_tool_id = :form_tool_id", [
      ':form_tool_id' => $id,
    ]);
    $data = $result->fetchObject();

    if ($data == FALSE) {
      throw new NotFoundHttpException();
    }

    /** @var \Drupal\webform\Entity\WebformSubmission $entity */
    $entity = \Drupal::service('entity.repository')->loadEntityByUuid('webform_submission', $data->submission_uuid);

    $data = $entity->getData();
    $data['id'] = $id;

    return [
      '#theme' => 'submission_print',
      '#submission' => $data,
    ];
  }

}
