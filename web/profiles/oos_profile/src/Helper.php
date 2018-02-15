<?php
/**
 * Created by PhpStorm.
 * User: oos
 * Date: 26.12.17
 * Time: 23:26
 */

namespace Drupal\oos_profile;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;

class Helper
{
  public static function managePanelizer() {
    try {
      $display = \Drupal::entityTypeManager()
        ->getStorage('entity_view_display')
        ->load('node.page.default');
      if ($display) {
        $settings = [
          'enable' => TRUE,
          'custom' => TRUE,
          'allow' => FALSE,
        ];
        if ($panelizer = \Drupal::service('panelizer')) {
          $panelizer->setPanelizerSettings($display->getTargetEntityTypeId(), $display->getTargetBundle(), $display->getMode(), $settings, $display);
        }
      }
    }
    catch (InvalidPluginDefinitionException $e) {
      watchdog_exception('oos_profile', $e);
    }
  }
}