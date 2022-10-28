<?php

namespace Drupal\form_tool_share\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Check access for form metadata endpoint.
 */
class FormMetadataAccessCheck implements AccessInterface {

  /**
   * When should we apply this check.
   *
   * @return string
   *   Check id.
   */
  public function appliesTo() {
    return '_form_metadata_access_check';
  }

  /**
   * Check that user name is one added in TokenAuth class.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   Current route.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User account. Should be anonymous.
   *
   * @return \Drupal\Core\Access\AccessResult|\Drupal\Core\Access\AccessResultAllowed
   *   Allowed or not.
   */
  public function access(Route $route, Request $request, AccountInterface $account): AccessResult|AccessResultAllowed {
    $userName = $account->getAccountName();
    $validToken = getenv('FORM_TOOL_TOKEN');

    if ($userName == $validToken) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

}
