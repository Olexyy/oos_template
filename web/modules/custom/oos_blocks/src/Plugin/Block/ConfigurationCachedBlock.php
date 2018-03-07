<?php

namespace Drupal\oos_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Class ConfigurationCachedBlock
 *
 * @package Drupal\oos_blocks\Plugin\Block
 */
abstract class ConfigurationCachedBlock extends BlockBase {

  /**
   * Cache tag generator.
   *
   * @param array $configuration
   *   Configuration array.
   *
   * @return string
   *   Hash.
   */
  protected function getCacheTag(array $configuration = NULL) {
    if (is_null($configuration)) {
      $configuration = $this->configuration;
    }
    return $this->getPluginId() . ':' . sha1(json_encode($configuration));
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), [$this->getCacheTag()]);
  }

}