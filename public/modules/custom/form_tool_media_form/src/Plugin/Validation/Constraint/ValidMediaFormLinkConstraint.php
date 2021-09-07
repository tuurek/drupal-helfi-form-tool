<?php

declare(strict_types = 1);

namespace Drupal\form_tool_media_form\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a ValidFormLink constraint.
 *
 * @Constraint(
 *   id = "ValidMediaFormLink",
 *   label = @Translation("ValidFormLink", context = "Validation"),
 * )
 */
final class ValidMediaFormLinkConstraint extends Constraint {

  /**
   * The error message.
   *
   * @var string
   */
  public string $notValidUrlErrorMessage = 'The url given (%value) is not pointing to a form tool script, copy the &lt;script&gt; tag from form tool as is';

  public string $notValidDomainErrorMessage = 'Given host (%value) is not valid, must be one of: %domains';

  public string $notValidTagErrorMessage = 'Given string (%value) is not valid script tag with a src attribute, copy the &lt;script&gt; tag from form tool as is';

}
