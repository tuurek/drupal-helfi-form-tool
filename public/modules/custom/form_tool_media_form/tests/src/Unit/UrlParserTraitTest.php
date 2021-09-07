<?php

declare(strict_types=1);

namespace Drupal\Tests\form_tool_media_form\Unit;

use Drupal\form_tool_media_form\UrlParserTrait;
use Drupal\Tests\UnitTestCase;

/**
 * Tests UrlParserTrait.
 *
 * @covers \Drupal\form_tool_media_form\UrlParseTrait
 * @group form_tool_media_form
 */
class UrlParserTraitTest extends UnitTestCase {

  use UrlParserTrait;

  /**
   * Tests that we can convert links to canonical form links.
   *
   * @dataProvider getTestFormUrlData
   */
  public function testFormUrl(string $url, string $expected) : void {
    $this->assertEquals($expected, $this->getFormUrl($url));
  }

  /**
   * The data provider for testGetLinkToForm().
   *
   * @return array
   *   The test data.
   */
  public function getTestFormUrlData() : array {
    return [
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
      ],
      [
        'https://hel-fi-form-tool.docker.so/embed?link=9UFyxc',
        'https://hel-fi-form-tool.docker.so/?link=9UFyxc',
      ],
      [
        'https://hel-fi-form-tool.docker.so/link/9uj8cj',
        'https://hel-fi-form-tool.docker.so/link/9uj8cj',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/unit/56241?p=1&t=accessibilityDetails',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/unit/56241?p=1&t=accessibilityDetails',
      ],
    ];
  }

  /**
   * Tests that we can convert links to embed urls.
   *
   * @dataProvider getTestEmbedLink
   */
  public function testEmbedLink(string $url, string $expected) : void {
    $this->assertEquals($expected, $this->getEmbedUrl($url));
  }

  /**
   * The data provider for testGetLinkToForm().
   *
   * @return array
   *   The test data.
   */
  public function getTestEmbedLink() : array {
    return [
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed?bbox=60.110894650782555,24.841289520263675,60.21824652560657,25.19937515258789&city=helsinki,espoo,vantaa,kauniainen',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/embed/?bbox=60.110,24.84,60.21,25.19&city=helsinki',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/embed/?bbox=60.110,24.84,60.21,25.19&city=helsinki',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/?bbox=60.110,24.84,60.21,25.19&city=helsinki',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/?bbox=60.110,24.84,60.21,25.19&city=helsinki',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/unit/56241?p=1&t=accessibilityDetails',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/unit/56241?p=1&t=accessibilityDetails',
      ],
      [
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/unit/56241?p=1&t=accessibilityDetails',
        'https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/embed/unit/56241?p=1&t=accessibilityDetails',
      ],
      [
        'https://hel-fi-form-tool.docker.so/embed?link=123',
        'https://hel-fi-form-tool.docker.so/embed?link=123',
      ],
      [
        'https://hel-fi-form-tool.docker.so/?link=345',
        'https://hel-fi-form-tool.docker.so/embed?link=345',
      ],
      [
        'https://hel-fi-form-tool.docker.so/link/678',
        'https://hel-fi-form-tool.docker.so/embed?link=678',
      ],
    ];
  }

}
