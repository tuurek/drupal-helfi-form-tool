<?php

declare(strict_types = 1);

namespace Drupal\form_tool_media_form\Plugin\Validation\Constraint;

use Drupal\form_tool_media_form\Plugin\media\Source\HelForm;
use League\Uri\Http;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Drupal\Component\Utility\Html;

/**
 * Validates the ValidFormLink constraint.
 */
final class ValidMediaFormLinkConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($item, Constraint $constraint) {
    foreach ($item->getValue() as $value) {
      $html = $value['value'];
      $uri = '';
      foreach (Html::load($html)->getElementsByTagName('script') as $script) {
        $uri = $script->getAttribute('src');
      }
      if ($uri == '') {
        $this->context->addViolation($constraint->notValidTagErrorMessage, [
          '%value' => $html,
        ]);
      }
      elseif (!strstr($uri, '/share.js') || !strstr($uri, '/webform/')) {
        $this->context->addViolation($constraint->notValidUrlErrorMessage, [
          '%value' => $uri,
        ]);
      }
      else {
        $uri = Http::createFromString($uri);

        if (!in_array($uri->getHost(), HelForm::VALID_URLS)) {
          $this->context->addViolation($constraint->notValidDomainErrorMessage, [
            '%value' => $uri->getHost(),
            '%domains' => implode(', ', HelForm::VALID_URLS),
          ]);
        }
      }

    }
  }

}
