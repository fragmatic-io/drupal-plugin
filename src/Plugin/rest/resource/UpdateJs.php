<?php

namespace Drupal\dxp_utilities\Plugin\rest\resource;

use Drupal\Core\Cache\Cache;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;

/**
 * UpdateJs custom rest post api that updates the JS if any change occurs.
 *
 * @RestResource(
 *   id = "update_js",
 *   label = @Translation("Update JS"),
 *   uri_paths = {
 *     "create" = "/api/v1/update/cache"
 *   }
 * )
 */
class UpdateJs extends ResourceBase {

  /**
   * Responds to POST requests.
   *
   * @param string $data
   *   Get data object on request.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data = NULL) {
    // Sending back response to the user.
    $response['message'] = 'JS has been updated';
    $status = 200;
    // Assigned it to null cache tag dxp_js:api_data.
    $cacheId = "dxp_js:api_data";
    \Drupal::cache()->set($cacheId, "", Cache::PERMANENT);

    $callService = \Drupal::service('dxp_css.api_css');
    $callService->callJsApi();
    $response = new ModifiedResourceResponse($response, $status);
    return $response;
  }

}
