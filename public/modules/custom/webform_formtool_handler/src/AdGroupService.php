<?php

namespace Drupal\webform_formtool_handler;

use GuzzleHttp\ClientInterface;

/**
 * Service for accessing different AD groups for access control.
 */
class AdGroupService {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * Constructs a SectorService object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   Http client. Guzzle.
   */
  public function __construct(ClientInterface $http_client,) {
    $this->httpClient = $http_client;
  }

  /**
   * Ad groups for forms.
   *
   * Hopefully this will get some support from ATV / Helsinki profiili.
   *
   * @return string[]
   *   Array containing ad groups.
   */
  public function getAdGroups(): array {
    return [
      'ad_group_1' => 'AD group 1',
      'ad_group_2' => 'AD group 2',
      'ad_group_3' => 'AD group 3',
      'ad_group_4' => 'AD group 4',
    ];
  }

}
