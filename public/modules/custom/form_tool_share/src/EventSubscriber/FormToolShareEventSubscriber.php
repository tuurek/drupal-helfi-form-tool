<?php

namespace Drupal\form_tool_share\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\form_tool_share\FormToolShareHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber to allow webform to be shared via an iframe.
 */
class FormToolShareEventSubscriber implements EventSubscriberInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a WebformShareEventSubscriber object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * Remove 'X-Frame-Options' from the response header for shared webforms.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The response event.
   */
  public function onResponse(FilterResponseEvent $event) {
    if (!FormToolShareHelper::isPage($this->routeMatch)) {
      return;
    }

    $response = $event->getResponse();
    $response->headers->remove('X-Frame-Options');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE] = ['onResponse'];
    return $events;
  }

}
