<?php

namespace Drupal\transliterate\Form;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Settings form for Module Filter.
 */
class TransliterateSettingsForm extends ConfigFormBase {

  private static $notTransliterableFieldNames = [
      'nid', 'uuid', 'vid', 'langcode', 'type', 'revision_timestamp', 'revision_uid', 'revision_log', 'status', 'uid',
      'created', 'changed', 'promote', 'sticky', 'default_langcode', 'revision_default', 'revision_translation_affected',
      'path', 'comment',
  ];

  private static $transliterableFieldTypes = [
    'list_string', 'text', 'text_long', 'text_with_summary', 'string', 'string_long', 'entity_reference',
    'link', 'file', 'image'
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'transliterate_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('transliterate.settings');
    $form = parent::buildForm($form, $form_state);

    /** @var NodeType $contentTypes[] */
    $contentTypes = \Drupal::entityTypeManager()
      ->getStorage('node_type')
      ->loadMultiple();


    $allowed_types = $config->get('content_types') ?? [];

    $form['content_types_tabs'] = array(
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Settings'),
    );
    $form['content_types'] = array(
      '#type' => 'container',
      '#tree' => TRUE,
    );

    foreach ($contentTypes as $contentType) {
      $type = $contentType->get('type');
      $name = $contentType->get('name');
      $ctype_settings = isset($allowed_types[$type]) ? $allowed_types[$type] : array();
      $fields_enabled = isset($ctype_settings['fields']) ? $ctype_settings['fields'] : array();
      $enabled = isset($ctype_settings['enabled']) ? $ctype_settings['enabled'] : 0;

      $form['content_types'][$type] = array(
        '#type' => 'details',
        '#title' => $name,
        '#group' => 'content_types_tabs',
      );

      $form['content_types'][$type]['enabled'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Enable auto transliteration for this content type.'),
        '#default_value' => $enabled,
      );

      $allowed_fields = array();

      /** @var BaseFieldDefinition[] $fields */
      $fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $type);
      foreach ($fields as $field) {
        if (in_array($field->getType(), self::$transliterableFieldTypes) &&
          !in_array($field->getName(), self::$notTransliterableFieldNames)) {
          $allowed_fields[$field->getName()] = $field->getLabel();
        }
      }

      $form['content_types'][$type]['fields'] = array(
        '#type' =>'checkboxes',
        '#options' => $allowed_fields,
        '#title' => t('Choose fields.'),
        '#default_value' => $fields_enabled,
        '#states' => array(
          'invisible' => array(
            ":input[name=\"content_types[$type][enabled]\"]" => array('checked' => FALSE),
          ),
        ),
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $valuesToSave = [];
    $contentTypeValues = array_key_exists('content_types', $values) ? $values['content_types'] : [];
    foreach ($contentTypeValues as $ct => $ctValue) {
      $valuesToSave[$ct]['enabled'] = array_key_exists('enabled', $ctValue) ? (bool)(int)$ctValue['enabled'] : false;
      $valuesToSave[$ct]['fields'] = [];
      if (array_key_exists('fields', $ctValue) && is_array($ctValue['fields'])) {
        foreach ($ctValue['fields'] as $fieldName => $fieldSetting) {
          if ((bool)$fieldSetting) {
            $valuesToSave[$ct]['fields'][$fieldName] = $fieldName;
          }
        }
      }
    }
    $this->config('transliterate.settings')
      ->set('content_types', $valuesToSave)
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['transliterate.settings'];
  }

}
