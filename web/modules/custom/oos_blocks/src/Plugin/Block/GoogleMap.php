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

class GoogleMap extends ConfigurationCachedBlock {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'width' => '500',
        'height' => '300',
        'address' => '49.830183,24.018121',
        'linkText' => 'Більша карта',
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#description' => $this->t('Address to be shown.'),
      '#default_value' => $this->configuration['address']
    ];
    $form['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#description' => $this->t('Width and measure.'),
      '#default_value' => $this->configuration['width'],
    ];
    $form['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#description' => $this->t('Height fnd measure.'),
      '#default_value' => $this->configuration['height']
    ];
    $form['linkText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#description' => $this->t('Text of "more" link.'),
      '#default_value' => $this->configuration['linkText']
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    if ($form_state instanceof  SubformStateInterface) {
      $existingCacheTag = $this->getCacheTag();
      $settings = $form_state->getCompleteFormState()->getValue('settings');
      $this->configuration['address'] = $settings['address'];
      $this->configuration['width'] = $settings['width'];
      $this->configuration['height'] = $settings['height'];
      $this->configuration['linkText'] = $settings['linkText'];
      $newCacheTag = $this->getCacheTag($settings);
      if ($existingCacheTag != $newCacheTag) {
        Cache::invalidateTags([$existingCacheTag]);
      }
    }
    parent::blockSubmit($form, $form_state);
  }


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
      '#width' => $this->configuration['width'],
      '#height' => $this->configuration['height'],
      '#static_scale' => 1,
      '#url_suffix' => $this->configuration['address'],
      '#zoom' => "14",
      '#link_text' => $this->configuration['linkText'],
      '#address_text' => "",
      '#map_type' => "m",
      '#langcode' => "uk",
      '#static_map_type' => "m",
      '#apikey' => "",
    ];
  }


}
