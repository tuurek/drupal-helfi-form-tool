<?php


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\webform\Access\WebformAccessResult;

/**
 * Override access control from
 */
class FormToolWsAccessHandler extends \Drupal\webform\WebformSubmissionAccessControlHandler {


  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    $access = parent::checkAccess($entity, $operation, $account);

    return $access;
  }

}