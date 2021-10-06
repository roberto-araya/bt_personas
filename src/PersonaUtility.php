<?php

namespace Drupal\bt_personas;

use Drupal\user\UserInterface;

/**
 * Utility class for Persona entity.
 */
class PersonaUtility implements PersonaUtilityInterface {

  /**
   * {@inheritdoc}
   */
  public static function fromUser(UserInterface $user) {
    return $user->get('personas')->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public static function rolesFromUserPersonas(UserInterface $user) {
    $personas = PersonaUtility::fromUser($user);
    /** @var \Drupal\bt_personas\PersonaInterface[] $personas */
    return array_values(
          array_reduce(
              $personas, function ($roles, $persona) {
                  $roles = array_merge($roles, $persona->getRoles());
                  return $roles;
              }, []
          )
      );
  }

  /**
   * {@inheritdoc}
   */
  public static function hasPersona(UserInterface $user, $persona) {
    $personas = static::fromUser($user);
    return in_array($persona, static::personaNames($personas));
  }

  /**
   * Returns a list of persona ids from a list of persona entities.
   *
   * @param \Drupal\bt_personas\PersonaInterface[] $personas
   *   The list of personas from which to get IDs.
   *
   * @return string[]
   *   The list of persona IDs.
   */
  public static function personaNames(array $personas) {
    return array_map(
          function ($persona) {
              return $persona->id();
          }, $personas
      );
  }

}
