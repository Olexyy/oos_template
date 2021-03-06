<?php

namespace Drupal\oos_blocks\Plugin\Block;


use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformStateInterface;
use Drupal\media\Entity\Media;

/**
 * Provides a "Parallax image" block.
 *
 * @Block(
 *   id = "parallax_image",
 *   admin_label = @Translation("Parallax image"),
 * )
 */

class ParallaxImage extends ConfigurationCachedBlock {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'media' => '',
      'text' => 'Вас вітає садок Святого Миколая!',
      'text_css' => 'color: yellow;',
      'image_css' => '',
    ] + parent::defaultConfiguration();
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
    $form['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Input text'),
      '#description' => $this->t('This text will be set in middle of parallax.'),
      '#default_value' => $this->configuration['text'],
    ];
    $form['text_css'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Input text css'),
      '#description' => $this->t('This css will be applied to text.'),
      '#default_value' => $this->configuration['text_css'],
    ];
    $form['image_css'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Input image css'),
      '#description' => $this->t('This css will be applied to image.'),
      '#default_value' => $this->configuration['image_css'],
    ];
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
    return $form['settings']['selected']['thumbnail'];
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
      $this->configuration['text'] = $settings['text'];
      $this->configuration['text_css'] = $settings['text_css'];
      $this->configuration['image_css'] = $settings['image_css'];
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
    if ($media = Media::load($this->configuration['media'])) {
      $image = $media->field_media_image->entity;
      $url = file_create_url($image->getFileUri());
    }
    else {
      $url = file_create_url("/modules/custom/oos_blocks/images/fable-landing.jpg");
    }
    $textCss = $this->configuration['text_css'];
    $imageCss = $this->configuration['image_css'];
    $content = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#allowed_tags' => ['div'],
      'parallax-text' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->configuration['text'],
        '#attributes' => [
          'class' => [
            'parallax-text',
          ],
          'style' => "text-align: center;
              margin-top: calc(100vw * 0.10);
              background-color: rgba(0, 0, 0, 0.3);
              padding: 15px;
              display: inline-block;
              color: white;
              font-size: 33px;
              font-weight: bold;
              {$textCss}",
        ], //#c9f01a;
      ],
      '#attributes' => [
        'class' => ['parallax'],
        'style' => "background-image: url({$url});
            width: 100%;
            height: calc(100vw * 0.5);
            background-attachment: fixed;
            background-position: center;
            background-repeat: repeat;
            background-size: cover;
            text-align: center;
            {$imageCss}",
      ],
    ];
    return $content;
  }

}
