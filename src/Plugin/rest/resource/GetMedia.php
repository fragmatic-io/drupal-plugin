<?php

namespace Drupal\dxp_utilities\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Provides a resource to retrieve media entities.
 *
 * @RestResource(
 *   id = "get_media_entities",
 *   label = @Translation("Get Media Entities"),
 *   uri_paths = {
 *     "canonical" = "/api/rest/media-get"
 *   }
 * )
 */
class GetMedia extends ResourceBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file url generator service.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->fileUrlGenerator = $container->get('file_url_generator');
    return $instance;
  }

  /**
   * Responds to GET requests to fetch media entities.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   */
  public function get() {
    // Set the number of items per page and the current page.
    $items_per_page = 10;
    $current_page = \Drupal::request()->query->get('page', 0);
    $name_filter = \Drupal::request()->query->get('name', NULL);

    // Negative page number ko handle karna
    $current_page = ($current_page < 0) ? 0 : $current_page;
    $start = $current_page * $items_per_page;

    // Media storage ko access karna
    $media_storage = $this->entityTypeManager->getStorage('media');
    $query = $media_storage->getQuery()->accessCheck(TRUE);

    // Agar name filter diya gaya hai to use query mein lagayenge
    if (!empty($name_filter)) {
      $query->condition('name', '%' . $name_filter . '%', 'LIKE');
    }

    // Sorting ko add karte hain taaki newest pehle aaye
    $query->sort('created', 'DESC');

    // Total count for pagination
    $count_query = clone $query;  // Alag se ek count query create karte hain
    $total_count = $count_query->count()->execute();
    $total_pages = ceil($total_count / $items_per_page);

    // Range lagake media items fetch karna
    $media_ids = $query->range($start, $items_per_page)->execute();

    // Prepare media results.
    $results = [];

    if (!empty($media_ids)) {
      $media_entities = $media_storage->loadMultiple($media_ids);
      foreach ($media_entities as $media) {
        // Assuming 'field_media_image' is the image field.
        $field_media_image = $media->get('field_media_image')->entity;

        // Generate the absolute URL for the image (full URL with domain)
        $image_url = $field_media_image ? $this->fileUrlGenerator->generateAbsoluteString($field_media_image->getFileUri()) : '';

        // Manually generate the relative path
        if ($field_media_image) {
          // Get the file URI, which is like 'public://2024-11/Screenshot.jpg'
          $file_uri = $field_media_image->getFileUri();

          // Remove the 'public://' part
          $relative_path = str_replace('public://', '/sites/default/files/', $file_uri);
        } else {
          $relative_path = '';
        }

        // Prepare the response data
        $results[] = [
          'field_media_image' => $relative_path,  // This should have the relative path
          'name' => $media->label(),
          'status' => $media->isPublished() ? 'On' : 'Off',
          'created' => \Drupal::service('date.formatter')->format($media->getCreatedTime(), 'custom', 'D, m/d/Y - H:i'),
          'mid' => $media->id(),
          'link' => $image_url,  // Full URL with domain
        ];
      }

      // Prepare the response.
      $response_data = [
        'results' => $results,
        'pager' => [
          'count' => $total_count,
          'pages' => $total_pages,
          'items_per_page' => $items_per_page,
          'current_page' => $current_page,
          'next_page' => ($current_page + 1) < $total_pages ? $current_page + 1 : NULL,
        ]
      ];
      $status = 200;
    }
    else {
      $response_data = [
        'results' => $results,
        'pager' => [
          'count' => $total_count,
          'pages' => $total_pages,
          'items_per_page' => $items_per_page,
          'current_page' => $current_page,
          'next_page' => ($current_page + 1) < $total_pages ? $current_page + 1 : NULL,
        ]
        ];
      $status = 200;
    }

    return new ModifiedResourceResponse($response_data, $status);
  }
}
