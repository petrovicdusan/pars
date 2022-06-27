<?php

namespace Drupal\transliterate\Plugin\Transliterator;

class Transliterator {

  /** @var string[] */
  private static $cyr2lat_trans_table = array (
    "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Џ" => "Dž", "Д" => "D", "Ђ" => "Đ", "Е" => "E", "Ж" => "Ž",
    "З" => "Z", "И" => "I", "Ј" => "J", "К" => "K", "Љ" => "Lj", "Л" => "L", "М" => "M", "Њ" => "Nj",
    "Н" => "N", "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "Ћ" => "Ć",
    "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C", "Ч" => "Č", "Ш" => "Š",
    "а" => "a", "б" => "b", "в" => "v", "г" => "g", "џ" => "dž", "д" => "d", "ђ" => "đ", "е" => "e", "ж" => "ž",
    "з" => "z", "и" => "i", "ј" => "j", "к" => "k", "љ" => "lj", "л" => "l", "м" => "m", "њ" => "nj", "н" => "n",
    "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "ћ" => "ć",
    "у" => "u", "ф" => "f", "х" => "h", "ц" => "c", "ч" => "č", "ш" => "š",
  );

  /** @var string[] */
  private static $cyr2lat_trans_table_ucase = array (
    "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Џ" => "DŽ", "Д" => "D", "Ђ" => "Đ", "Е" => "E", "Ж" => "Ž",
    "З" => "Z", "И" => "I", "Ј" => "J", "К" => "K", "Љ" => "LJ", "Л" => "L", "М" => "M", "Њ" => "NJ", "Н" => "N",
    "О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T", "Ћ" => "Ć",
    "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C", "Ч" => "Č", "Ш" => "Š",
  );

  /**
   * Tranliterate cyrilic (html) text to latin equivalent. Simple strtr().
   *
   * @param $str
   * @return string
   */
  public static function cyr2lat($str) {
    if (empty($str)) return '';

    $splitted = preg_split('~(<[^>]+>|\&[a-z]+;|\&0x[0-9a-f]+;|\&\#[0-9]+;)~sSi', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
    $pattern = join('', array_keys(self::$cyr2lat_trans_table_ucase));
    $pattern_nonchar = join('', array_keys(self::$cyr2lat_trans_table));
    // parni su sadrzaj, neparni su delimiteri
    for ($i = 0, $l = count($splitted), $out = ''; $i < $l; $i++) {
      if ($i % 2) {
        $out .= preg_replace_callback('~(\s(title|alt))="([^"]+)"~', function ($matches) {
          return self::cyr2lat($matches[0]);
        }, $splitted[$i]);
      } else {
        $splitted[$i] = preg_replace_callback(
          "~(^|[^$pattern_nonchar])([$pattern]+)(?=($|[^$pattern_nonchar]))~u",
          function ($matches) {
            return strtr($matches[0], self::$cyr2lat_trans_table_ucase);
          },
          $splitted[$i]
        );
        $out .= strtr($splitted[$i], self::$cyr2lat_trans_table);
      }
    }
    return $out;
  }

  /**
   * Tranliterate cyrilic (html) text to latin equivalent. Simple strtr().
   *
   * @param $str
   * @return string
   */
  public static function lat2cir($str) {
    if (empty($str)) return '';

    $splitted = preg_split('~(<[^>]+>|\&[a-z]+;|\&0x[0-9a-f]+;|\&\#[0-9]+;)~sSi', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
    $pattern = join('', array_values(self::$cyr2lat_trans_table_ucase));
    $pattern_nonchar = join('', array_values(self::$cyr2lat_trans_table));
    // parni su sadrzaj, neparni su delimiteri
    for ($i = 0, $l = count($splitted), $out = ''; $i < $l; $i++) {
      if ($i % 2) {
        $out .= preg_replace_callback('~(\s(title|alt))="([^"]+)"~', function ($matches) {
          return self::cyr2lat($matches[0]);
        }, $splitted[$i]);
      } else {
        $splitted[$i] = preg_replace_callback(
          "~(^|[^$pattern_nonchar])([$pattern]+)(?=($|[^$pattern_nonchar]))~u",
          function ($matches) {
            return strtr($matches[0], array_flip(self::$cyr2lat_trans_table_ucase));
          },
          $splitted[$i]
        );
        $out .= strtr($splitted[$i], array_flip(self::$cyr2lat_trans_table));
      }
    }
    return $out;
  }

}
