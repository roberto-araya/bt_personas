<?php

namespace Drupal\bt_personas\Batch;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

use Drupal\bt_personas\PersonaInterface;

/**
 * Processor for Personas's batch operation.
 */
class Processor {

  /**
   * The number of users to process per batch operation.
   */
  const BATCH_SIZE = 20;

  /**
   * The entity_type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The persona that was updated and triggering this batch process.
   *
   * @var \Drupal\bt_personas\PersonaInterface
   */
  protected $persona;

  /**
   * The maximum number of users to process per batch operation.
   *
   * @var int
   */
  protected $batchSize;

  /**
   * Set entityTypeManager and BachSize.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->setBatchSize(static::BATCH_SIZE);
  }

  /**
   * Set the number of elements of the batch.
   */
  public function setBatchSize($size) {
    $this->batchSize = $size;
  }

  /**
   * Backend bash process.
   */
  public function process(PersonaInterface $persona) {
    $this->persona = $persona;
    $batch = $this->getBatch();
    batch_set($batch);
    if (function_exists('drush_backend_batch_process')) {
      drush_backend_batch_process();
    }
    else {
      $redirect = batch_process(Url::fromRoute('entity.persona.collection'));
      if ($redirect) {
        $redirect->send();
      }
    }
  }

  /**
   * Batch's process function.
   */
  public static function processBatch($uids, &$context) {
    $entity_type_manager = \Drupal::getContainer()->get('entity_type.manager');
    $user_storage = $entity_type_manager->getStorage('user');
    $users = $user_storage->loadMultiple($uids);
    foreach ($users as $user) {
      $user->save();
    }
  }

  /**
   * Return a batch.
   */
  protected function getBatch() {
    $operations = [];
    $uids = $this->getUids();
    $operations = array_reduce(
          array_chunk($uids, $this->batchSize), function ($ops, $chunk) {
              $ops[] = [
              [static::class, 'processBatch'],
              [$chunk],
              ];
              return $ops;
          }, []
      );
    return [
      'operations' => $operations,
    ];
  }

  /**
   * Get the Users ids.
   */
  protected function getUids() {
    $user_storage = $this->entityTypeManager->getStorage('user');
    $query = $user_storage->getQuery();
    $query->condition('personas', $this->persona->id(), 'CONTAINS');
    return array_values($query->execute());
  }

}
