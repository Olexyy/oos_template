<?php

namespace Drupal\oos_blocks\Plugin\Block;


use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\media\Entity\Media;

/**
 * Provides a "Google map" block.
 *
 * @Block(
 *   id = "google_map",
 *   admin_label = @Translation("Google map"),
 * )
 */

class GoogleMap extends BlockBase {

   /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'simple_gmap_output',
      '#include_map' => "1",
      '#include_static_map' => "0",
      '#include_link' => "0",
      '#include_text' => "0",
      '#width' => "500",
      '#height' => "300",
      '#static_scale' => 1,
      '#url_suffix' => '49.830183,24.018121',
      '#zoom' => "14",
      '#link_text' => "Більша карта",
      '#address_text' => "",
      '#map_type' => "m",
      '#langcode' => "uk",
      '#static_map_type' => "m",
      '#apikey' => "",
    ];
  }


}
