<?php

namespace Drupal\oos_blocks\Plugin\Block;


use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\Core\Image\Image;
use Drupal\Core\Url;
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
    $hiddenId = Html::getUniqueId('parallax_image-hidden-id');
    $selectedId = Html::getUniqueId('parallax_image-selected-id');
    $uri = NULL;
    if ($form_state instanceof  SubformStateInterface &&
      ($triggering_element = $form_state->getCompleteFormState()->getTriggeringElement())) {
      if ($triggering_element['#ajax']['event'] == 'entity_browser_value_updated') {
        $mediaId = $triggering_element['#value'];
        $media = Media::load(explode(':', $mediaId)[1]);
        if ($image = $media->field_media_image->entity) {
          $uri = $image->getFileUri();
        }
      }
    }
    else if ($media = Media::load($this->configuration['media'])) {
      if ($image = $media->field_media_image->entity) {
        $uri = $image->getFileUri();
      }
    }
    $form['selected'] = [
      '#type' => 'container',
      'thumbnail' => [
        '#attributes' => ['id' => $selectedId],
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => $uri,
        '#attached'=>['library' => ['entity_browser/entity_reference']],
      ],
      'target_id' => [
        '#type' => 'hidden',
        '#id' => $hiddenId,
        '#attributes' => ['id' => $hiddenId],
        '#ajax' => [
          'callback' => [get_class($this), 'updateWidgetCallback'],
          'wrapper' => $selectedId,
          'event' => 'entity_browser_value_updated',
        ],
      ],
    ];
    $form['entity_browser'] = [
      '#type' => 'entity_browser',
      '#entity_browser' => 'modal',
      '#cardinality' => 1,
      '#custom_hidden_id' => $hiddenId,
      '#process' => [
        ['\Drupal\entity_browser\Element\EntityBrowserElement', 'processEntityBrowser'],
        [get_called_class(), 'processEntityBrowser'],
      ],
    ];
    //$form['#attached']['library'][] = 'entity_browser/entity_reference';
    return $form;
  }

  /**
   * Render API callback: Processes the entity browser element.
   */
  public static function processEntityBrowser(&$element, FormStateInterface $form_state, &$complete_form) {
    $uuid = key($element['#attached']['drupalSettings']['entity_browser']);
    $element['#attached']['drupalSettings']['entity_browser'][$uuid]['selector'] = '#' . $element['#custom_hidden_id'];
    return $element;
  }

  /**
   * AJAX form callback.
   */
  public static function updateWidgetCallback(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    if (!empty($trigger['#ajax']['event']) && $trigger['#ajax']['event'] == 'entity_browser_value_updated') {
      return $form['settings']['selected']['thumbnail'];
    }
    return $form['selected']['thumbnail'];
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    if ($form_state instanceof  SubformStateInterface) {
      $existingCacheTag = $this->getCacheTag();
      $settings = $form_state->getCompleteFormState()->getValue('settings');
      if ($settings['selected']['target_id']) {
        $this->configuration['media'] = explode(':', $settings['selected']['target_id'])[1];
      }
      $newCacheTag = $this->getCacheTag($settings);
      if ($existingCacheTag != $newCacheTag) {
        Cache::invalidateTags([$existingCacheTag]);
      }
    }
    parent::blockSubmit($form, $form_state);
  }

  /**
   * Cache tag generator.
   *
   * @param array $configuration
   *   Configuration array.
   *
   * @return string
   *   Hash.
   */
  private function getCacheTag(array $configuration = NULL) {
    if (is_null($configuration)) {
      $configuration = $this->configuration;
    }
    return $this->getPluginId() . ':' . sha1(json_encode($configuration));
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
                        margin-top: calc(100vw * 0.2);
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
            width: 100%;
            height: calc(100vw * 0.5);
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            top: -50px;
            text-align: center;",
        ],
      ];
      return $content;
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), [$this->getCacheTag()]);
  }

}
