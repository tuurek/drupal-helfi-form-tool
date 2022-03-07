<?php

namespace Drupal\form_tool_handler\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "form_tool_handler_block",
 *   admin_label = @Translation("FormTool Block"),
 *   category = @Translation("form_tool_handler")
 * )
 */
class FormToolBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
