<?php

namespace Drupal\oos_blocks\Plugin\Field\FieldWidget;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldWidget\DateTimeWidgetBase;

/**
 * Plugin implementation of the 'datetime_year_list' widget.
 *
 * @FieldWidget(
 *   id = "datetime_year_list",
 *   label = @Translation("Select list(year)"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateTimeYearListWidget extends DateTimeWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'range' => '30',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    // Wrap all of the select elements with a fieldset.
    $element['#theme_wrappers'][] = 'fieldset';

    $range = $this->getSetting('range') / 2;

    $drupalDateTime = $element['value']['#default_value'] instanceof DrupalDateTime?
      $element['value']['#default_value'] : new DrupalDateTime();
    // Make configurable.
    $year = (int)$drupalDateTime->format('Y');
    $options = range($year - $range, $year + $range);
    $options = array_combine($options, $options);

    $element['value'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $year,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    // The widget form element type has transformed the value to a
    // DrupalDateTime object at this point. We need to convert it back to the
    // storage timezone and format.
    foreach ($values as &$item) {
      if (!empty($item['value']) && is_numeric($item['value'])) {
        $date = DrupalDateTime::createFromFormat('Y', $item['value']);
        switch ($this->getFieldSetting('datetime_type')) {
          case DateTimeItem::DATETIME_TYPE_DATE:
            // If this is a date-only field, set it to the default time so the
            // timezone conversion can be reversed.
            datetime_date_default_time($date);
            $format = DATETIME_DATE_STORAGE_FORMAT;
            break;

          default:
            $format = DATETIME_DATETIME_STORAGE_FORMAT;
            break;
        }
        // Adjust the date for storage.
        $date->setTimezone(new \DateTimezone(DATETIME_STORAGE_TIMEZONE));
        $item['value'] = $date->format($format);
      }
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $options = range (5, 50, 5);
    $options = array_combine($options, $options);
    $element['range'] = [
      '#type' => 'select',
      '#title' => t('Range for years select.'),
      '#default_value' => $this->getSetting('range'),
      '#options' => $options,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Yeas range: @range', ['@range' => $this->getSetting('range')]);

    return $summary;
  }

}
