<?php

namespace Drupal\form_tool_handler;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\helfi_atv\AtvService;
use Drupal\webform\WebformSubmissionStorage;
use GuzzleHttp\Exception\GuzzleException;
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
   * Save webform submission data from the 'webform_submission_data' table.
   *
   * @param array $webform_submissions
   *   An array of webform submissions.
   */
  protected function loadData(array &$webform_submissions) {
    parent::loadData($webform_submissions);

    /** @var \Drupal\webform\Entity\WebformSubmission $submission */
//    foreach ($webform_submissions as $submission) {
//
//      $result = $this->connection->query("SELECT document_uuid FROM {form_tool} WHERE submission_uuid = :submission_uuid", [
//        ':submission_uuid' => $submission->uuid(),
//      ]);
//      $data = $result->fetchObject();
//
//      if ($data !== FALSE) {
//        /** @var \Drupal\helfi_atv\AtvDocument $document */
//        try {
//          $document = $this->atvService->getDocument($data->document_uuid);
//
//          $documentContent = $document->getContent();
//
//          $submission->setData($documentContent);
//        }
//        catch (\Exception | GuzzleException $e) {
//          $this->loggerFactory->get('form_tool_handler')->error($e->getMessage());
//        }
//
//      }
//    }
  }

}
