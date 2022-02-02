<?php

namespace Drupal\form_tool_webform_print\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\form_tool_webform_print\FormToolWebformPrintHelper;

/**
 * Sets the theme for the webform share page.
 *
 * @see \Drupal\form_tool_webform_print\Controller\FormToolWebformPrintController::page
 * @see page--webform-share.html.twig
 */
class FormToolWebformPrintThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * The system theme config object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a FormToolWebformPrintThemeNegotiator object.
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
    return FormToolWebformPrintHelper::isPage($route_match);
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    /** @var \Drupal\webform\WebformInterface $webform */
    $webform = $route_match->getParameter('webform');
    return $webform->getSetting('print_theme_name', TRUE)
      ?: $this->configFactory->get('system.theme')->get('default');
  }

}
