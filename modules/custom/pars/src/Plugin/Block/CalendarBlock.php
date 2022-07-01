<?php

namespace Drupal\pars\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Language\Language;
use Drupal\pars\Bundle\Calendar\ParsCalendar;
use Drupal\pars\Bundle\Calendar\ParsCalendarRenderer;

/**
 * @Block(
 *   id = "calendar_block",
 *   admin_label = @Translation("Calendar block"),
 *   category = @Translation("Calendar block")
 * )
 */
class CalendarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $renderer = new ParsCalendarRenderer();
    $variables = [
      'language' => \Drupal::languageManager()->getCurrentLanguage()->getId()
    ];
    return [
      '#type' => 'markup',
      '#markup' => $renderer->render($variables),
      '#attached' => [
        'library' => [
          'pars/calendar'
        ]
      ]
    ];
  }

  /**
   * @return int
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
