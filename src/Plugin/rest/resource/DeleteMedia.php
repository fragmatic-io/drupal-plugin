<?php

namespace Drupal\dxp_utilities\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a resource to generate delete media entities.
 *
 * @RestResource(
 *   id = "delete_media_entities",
 *   label = @Translation("Delete Media Entities"),
 *   uri_paths = {
 *     "canonical" = "/api/rest/media-delete",
 *     "create" = "/api/rest/media-delete"
 *   }
 * )
 */
class DeleteMedia extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->currentUser = $container->get('current_user');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * Responds to POST requests.
   *
   * @param string $data
   *   Request data.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data = NULL) {

    // Check if media_id is set and not empty.
    if (isset($data['media_id']) && !empty($data['media_id'])) {
      $media = $this->entityTypeManager->getStorage('media')->load($data['media_id']);
      // Check if media exists with given media ID.
      if ($media) {
        // Check if current user has permission to delete media entities.
        if ($media->access('delete', $this->currentUser)) {
          $media->delete();
          $message = $this->t('Media is deleted with ID: @id', ['@id' => $data['media_id']]);
          $status = 200;
        }
        else {
          $status = 403;
          $message = $this->t("The current user doesn't have permission to delete this media");
        }
      }
      else {
        $status = 404;
        $message = $this->t("Media doesn't exist with the given ID: @id", ['@id' => $data['media_id']]);
      }
    }
    else {
      $status = 400;
      $message = $this->t('Provide media id');
    }

    return new ModifiedResourceResponse(['message' => $message ? $message : 'Something went wrong'], isset($status) ? $status : 500);
  }

}
