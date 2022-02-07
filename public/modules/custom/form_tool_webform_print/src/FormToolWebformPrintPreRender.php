<?php

namespace Drupal\form_tool_webform_print;

use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Implements trusted prerender callbacks for the Webform print module.
 *
 * @internal
 */
class FormToolWebformPrintPreRender implements TrustedCallbackInterface {

  /**
   * Prerender callback for page.
   */
  public static function page($element) {
    if (!FormToolWebformPrintHelper::isPage()) {
      return $element;
    }

    // Remove all theme wrappers from the page template.
    $element['#theme_wrappers'] = [];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['page'];
  }

}
