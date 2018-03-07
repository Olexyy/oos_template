<?php

namespace Drupal\oos_blocks\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;

/**
 * Provides a "Social" block.
 *
 * @Block(
 *   id = "social",
 *   admin_label = @Translation("Social"),
 * )
 */

class Social extends ConfigurationCachedBlock {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'facebook' => '',
      'twitter' => '',
      'googlePlus' => '',
      'size' => '30',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['facebook'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Input Facebook url'),
      '#description' => $this->t('This social will not show if empty.'),
      '#default_value' => $this->configuration['facebook'],
    ];
    $form['twitter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Input Twitter url'),
      '#description' => $this->t('This social will not show if empty.'),
      '#default_value' => $this->configuration['twitter']
    ];
    $form['googlePlus'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Input Google Plus url'),
      '#description' => $this->t('This social will not show if empty.'),
      '#default_value' => $this->configuration['googlePlus']
    ];
    $form['size'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon size'),
      '#description' => $this->t('Input icon size in pixels.'),
      '#default_value' => $this->configuration['size']
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
      $this->configuration['facebook'] = $settings['facebook'];
      $this->configuration['twitter'] = $settings['twitter'];
      $this->configuration['googlePlus'] = $settings['googlePlus'];
      $this->configuration['size'] = $settings['size'];
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
    $socials = ['facebook', 'twitter', 'googlePlus'];
    $content = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['social-block-wrapper'],
      ],
    ];
    foreach ($socials as $social) {
      if ($url = $this->configuration[$social]) {
        $size = $this->configuration['size'];
        $image = file_create_url("/modules/custom/oos_blocks/images/{$social}.png");
        $content[$social] = [
          '#type' => 'html_tag',
          '#tag' => 'a',
          '#attributes' => [
            'href' => $url,
          ],
          $social.'_icon' => [
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => [
              'class' => ["icon-{$social}"],
              'style' => "background-image: url({$image});
                        width: {$size}px;
                        height: {$size}px;
                        background-position: center;
                        background-size: cover;
                        display: inline-block",
            ],
          ],
        ];
      }
    }
    return $content;
  }

}
