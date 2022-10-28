<?php

namespace Drupal\webform_formtool_handler;

use Drupal\webform\Access\WebformAccessResult;
use Drupal\webform\WebformSubmissionAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Override access control from.
 */
class FormToolWsAccessHandler extends WebformSubmissionAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(WebformSubmissionInterface|EntityInterface $entity, $operation, AccountInterface $account) {

    $webform = $entity->getWebform();
    $webformOwner = $webform->getOwner();
    $webformOwnerRoles = $webformOwner->getRoles();
    $thirdPartySettings = $webform->getThirdPartySettings('form_tool_webform_parameters');

    // Admins have access always.
    if (in_array(['admin', 'verkkolomake_admin'], $webformOwnerRoles)) {
      return WebformAccessResult::allowed();
    }

    // Webform owner has access to submission when in WIP state.
    if (($thirdPartySettings["status"] == 'wip') && $webformOwner->id() == $account->id()) {
      return WebformAccessResult::allowed();
    }

    // Load saved data for this submission.
    $result = \Drupal::service('database')
      ->query("SELECT document_uuid,admin_owner,admin_roles,user_uuid FROM {form_tool_map} WHERE submission_uuid = :submission_uuid", [
        ':submission_uuid' => $entity->get('uuid')->value,
      ]);
    $data = $result->fetchObject();

    $adminRoles = explode(',', $data->admin_roles);
    $userRoles = $account->getRoles();
    foreach ($adminRoles as $rid) {
      // If user has a role to access this webform submission.
      if (in_array($rid, $userRoles)) {
        return WebformAccessResult::allowed();
      }
    }

    // Admin owner has access if state is WIP.
    if (($thirdPartySettings["status"] == 'wip') && $data->admin_owner == $account->getEmail()) {
      return WebformAccessResult::allowed();
    }

    $helProfiiliData = \Drupal::service('helfi_helsinki_profiili.userdata');
    $userData = $helProfiiliData->getUserData();

    if (!$userData) {
      return WebformAccessResult::forbidden();
    }

    // User can access their own submission.
    if ($data->user_uuid == $userData["sub"]) {
      return WebformAccessResult::allowed();
    }

    // $access = parent::checkAccess($entity, $operation, $account);
    // return $access;
    return WebformAccessResult::forbidden();
  }

}
