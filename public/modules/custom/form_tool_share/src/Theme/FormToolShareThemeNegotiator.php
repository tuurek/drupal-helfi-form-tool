<?php

namespace Drupal\form_tool_share\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\form_tool_share\FormToolShareHelper;

/**
 * Sets the theme for the webform share page.
 *
 * @see \Drupal\form_tool_share\Controller\FormToolShareController::page
 * @see page--form-tool-share.html.twig
 */
class FormToolShareThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * The system theme config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a WebformShareThemeNegotiator object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return FormToolShareHelper::isPage($route_match);
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    /** @var \Drupal\webform\WebformInterface $webform */
    $webform = $route_match->getParameter('webform');
    return $webform->getSetting('share_theme_name', TRUE)
      ?: $this->configFactory->get('system.theme')->get('default');
  }

}
