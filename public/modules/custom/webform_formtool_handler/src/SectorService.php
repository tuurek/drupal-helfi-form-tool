<?php

namespace Drupal\webform_formtool_handler;

use GuzzleHttp\ClientInterface;

/**
 * SectorService service.
 */
class SectorService {

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
   *  Http client. Guzzle.
   */
  public function __construct(ClientInterface $http_client,) {
    $this->httpClient = $http_client;
  }

  /**
   * Sector list for forms.
   *
   * Hopefully this will get downloaded from some external service.
   *
   * @return string[]
   */
  public function getSectorList(): array {
    return [
      'KYMP' => 'KYMP',
      'KUVA' => 'KUVA',
      'SOTE' => 'SOTE',
      'KASKO' => 'KASKO',
    ];
  }

}
