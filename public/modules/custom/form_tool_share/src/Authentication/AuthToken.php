<?php

namespace Drupal\form_tool_share\Authentication;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Drupal\Core\Session\UserSession;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authentication provider to validate requests with token in header.
 */
class AuthToken implements AuthenticationProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request): bool {
    return $request->headers->has('X-Auth-Token');
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request): UserSession|AccountInterface|NULL {
    $token = $request->headers->get('X-Auth-Token');
    $validToken = getenv('FORM_TOOL_TOKEN');

    if ($token === $validToken) {
      // Return a session if the request passes the validation.
      // Set valid token as username.
      $us = new UserSession();
      $us->name = $validToken;

      return $us;
    }
    return NULL;
  }

}
