<?php

namespace Drupal\form_tool_handler;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\helfi_atv\AtvService;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformSubmissionStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Override loading of WF submission from data from ATV.
 *
 * This could be used overriding the saving as well,
 * but for now this is enough.
 */
class FormToolSubmissionStorage extends WebformSubmissionStorage {

  /**
   * Atv service object.
   *
   * @var \Drupal\helfi_atv\AtvService
   */
  protected AtvService $atvService;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(
    ContainerInterface $container,
    EntityTypeInterface $entity_type): WebformSubmissionStorage|EntityHandlerInterface {

    /** @var \Drupal\webform\WebformSubmissionStorage $instance */
    $instance = parent::createInstance($container, $entity_type);

    /** @var \Drupal\helfi_atv\AtvService atvService */
    $instance->atvService = $container->get('helfi_atv.atv_service');
    $instance->connection = $container->get('database');

    return $instance;
  }

  /**
   * Override data saving to make sure no data gets saved.
   *
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   Webform submission object.
   * @param bool $delete_first
   *   Delete data first?
   */
  public function saveData(WebformSubmissionInterface $webform_submission, $delete_first = TRUE) {
    // Do nothing, since we do not want to store form data to local database.
  }

  /**
   * Overide data loading to make sure no data is used.
   *
   * This method could be used to load data from ATV.
   *
   * @param array $webform_submissions
   *   Submissions to load data to.
   */
  public function loadData(array &$webform_submissions) {
    // Do nothing, we do not want to use any normal data.
  }

  // /**
  // * Save webform submission data from the 'webform_submission_data' table.
  // *
  // * @param array $webform_submissions
  // *   An array of webform submissions.
  // */
  // Protected function loadData(array &$webform_submissions) {
  // parent::loadData($webform_submissions);
  //
  // /** @var \Drupal\webform\Entity\WebformSubmission $submission */
  // // Foreach ($webform_submissions as $submission) {
  // //
  // //      $result = $this->connection->query("SELECT document_uuid
  // FROM {form_tool} WHERE submission_uuid = :submission_uuid", [
  // //        ':submission_uuid' => $submission->uuid(),
  // //      ]);
  // //      $data = $result->fetchObject();
  // //
  // //      if ($data !== FALSE) {
  // //        /** @var \Drupal\helfi_atv\AtvDocument $document */
  // //        try {
  // //          $document = $this->atvService->getDocument($data->docume
  // nt_uuid);
  // //
  // //          $documentContent = $document->getContent();
  // //
  // //          $submission->setData($documentContent);
  // //        }
  // //        catch (\Exception | GuzzleException $e) {
  // //          $this->loggerFactory->get('form_tool_handler')->error($e->
  // getMessage());
  // //        }
  // //
  // //      }
  // //    }
  // }.
}
