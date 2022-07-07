<?php

namespace Drupal\pars\Plugin\Block;

use Drupal\Core\Block\BlockBase;
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

    return [
      '#type' => 'markup',
      '#markup' => $renderer->render(null, null, \Drupal::languageManager()->getCurrentLanguage()->getId()),
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
