<?php

namespace Drupal\personas;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the persona entity type.
 *
 * @see \Drupal\persona\Entity\Persona
 */
class PersonaAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      default:
        return parent::checkAccess($entity, $operation, $account);
    }
  }

}
