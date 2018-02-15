<?php

namespace Drupal\oos_blocks\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\Image\Image;
use Drupal\entity_browser\Element\EntityBrowserElement;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Provides a "Parallax image" block.
 *
 * @Block(
 *   id = "parallax_image",
 *   admin_label = @Translation("Parallax image"),
 * )
 */

class ParallaxImage extends BlockBase {


  public function defaultConfiguration() {
      return ['media' => ''] + parent::defaultConfiguration();
  }

    /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['entity_browser'] = [
      '#type' => 'entity_browser',
      '#entity_browser' => 'default_entity_browser',
      '#cardinality' => 1,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    if ($form_state instanceof  SubformStateInterface) {
      $settings = $form_state->getCompleteFormState()->getValue('settings');
      if (isset($settings['entity_browser']['entities'][0])) {
        $this->configuration['media'] = $settings['entity_browser']['entities'][0]->id();
      }
    }
    parent::blockSubmit($form, $form_state);
  }

    /**
   * {@inheritdoc}
   */
  public function build() {
    
    if ($media = Media::load($this->configuration['media'])) {
      $image = $media->field_media_image->entity;
      $url = file_create_url($image->getFileUri());
      $content = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#allowed_tags' => ['div'],
        'parallax-text' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => 'Вас вітає Садок Святого Миколая!',
          '#attributes' => [
            'class' => [
              'parallax-text',
            ],
            'style' => "text-align: center;
                        margin-top: 200px;
                        background-color: rgba(0, 0, 0, 0.3);
                        padding: 15px;
                        display: inline-block;
                        color: #c9f01a;
                        font-size: 25px;
                        font-weight: bold;",
          ],
        ],
        '#attributes' => [
          'class' => ['parallax'],
          'style' => "background-image: url({$url});
                    min-height: 500px;
                    background-attachment: fixed;
                    background-position: center;
                    background-repeat: no-repeat;
                    background-size: 100% auto;
                    top: -50px;
                    text-align: center;",
        ],
        '#cache' => ['max-age' => 0,],
      ];
      return $content;
    }

    return [];
  }
}