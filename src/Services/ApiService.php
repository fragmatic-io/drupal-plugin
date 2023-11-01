<?php

namespace Drupal\dxp_utilities\Services;

use Drupal\Core\Cache\Cache;

/**
 * Class for calling API service of JS and CSS.
 *
 * @package \Drupal\dxp_utilities\Services
 */
class ApiService {

  /**
   * API call for updating the css data.
   */
  public function callApi() {
    try {
      // Clearing plugin cache after calling the service.
      \Drupal::service('plugin.cache_clearer')->clearCachedDefinitions();
      \Drupal::service('router.builder')->rebuild();
      // Fetching the middleware url from the config.
      $middleware = \Drupal::config('dxp_utilities.middleware.settings')->get('dxp_middleware_url');
      $middleware = str_replace("'", "", $middleware);
      // Creating url for api.
      $url = $middleware . '/slot/search/filtered?format=css';
      // Sending Get Request for getting the data.
      $request = file_get_contents($url);
      // Storing the data in cache.
      $cacheId = "dxp_css:api_data";
      \Drupal::cache()->set($cacheId, $request, Cache::PERMANENT);
      $response['message'] = 'Service is called';
    }
    catch (RequestException $exception) {
      $this->logger->error($exception->getMessage());
      $response['error'] = 500;
    }
    return $response;
  }

  /**
   * API call for updating JS data.
   */
  public function callJsApi() {
    try {
      // Clearing the plugin cache after calling the service.
      \Drupal::service('plugin.cache_clearer')->clearCachedDefinitions();
      \Drupal::service('router.builder')->rebuild();
      // Fetching the middleware url from the config.
      $middleware = \Drupal::config('dxp_utilities.middleware.settings')->get('dxp_middleware_url');
      $middleware = str_replace("'", "", $middleware);
      // Creating url for api.
      $url = $middleware . '/others/cache';
      // Sending Get Request for getting the data.
      $request = file_get_contents($url);
      // Storing the data in cache.
      $cacheIdJs = "dxp_js:api_data";
      \Drupal::cache()->set($cacheIdJs, $request, Cache::PERMANENT);
      $response['message'] = 'JS API Service is called';
    }
    catch (RequestException $exception) {
      $this->logger->error($exception->getMessage());
      $response['error'] = 500;
    }
    return $response;
  }

}
