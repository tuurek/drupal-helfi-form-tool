<?php

namespace Drupal\webform_formtool_handler;

use Drupal\webform\WebformSubmissionAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Override access control from.
 */
class FormToolWsAccessHandler extends WebformSubmissionAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    $access = parent::checkAccess($entity, $operation, $account);

    return $access;
  }

}
