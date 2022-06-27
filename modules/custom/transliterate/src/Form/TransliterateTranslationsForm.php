<?php

namespace Drupal\transliterate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\transliterate\TransliterateTranslationSync;

/**
 * Settings form for Module Filter.
 */
class TransliterateTranslationsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'transliterate_translations_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['info'] = array(
      '#markup' => $this->t('This operation will remove all latin translations and recreate it using serbian cyrillic transliterated locales'),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    // By default, render the form using system-config-form.html.twig.
    $form['#theme'] = 'system_config_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   * @throws \Drupal\locale\StringStorageException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    TransliterateTranslationSync::syncTranslations();

    $this->messenger()->addStatus($this->t('Translations have been saved successfully saved.'));
  }

}
