<?php

namespace Drupal\transliterate;

use Drupal\transliterate\Plugin\Transliterator\Transliterator;

class TransliterateTranslationSync {

  /**
   * Sync serbian cyrillic and serbian latin translations
   *
   * @throws \Drupal\locale\StringStorageException
   */
  public static function syncTranslations() {
    /** @var \Drupal\locale\StringDatabaseStorage $storage */
    $storage = \Drupal::service('locale.storage');
    $srTranslationStrings = $storage->getTranslations(['translated' => true, 'language' => SR]);
    $latTranslationStrings = $storage->getTranslations(['translated' => true, 'language' => SR_LAT]);
    foreach ($latTranslationStrings as $latTranslationString) {
      $latTranslationString->delete();
    }
    foreach ($srTranslationStrings as $srTranslationString) {
      $transliteratedString = Transliterator::cyr2lat($srTranslationString->getString());

      // Create translation. If one already exists, it will be replaced.
      $storage->createTranslation(array(
        'lid' => $srTranslationString->lid,
        'language' => SR_LAT,
        'translation' => $transliteratedString,
      ))->save();
    }
  }

}
