<?php

namespace Drupal\pars\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\pars\Bundle\Calendar\ParsCalendarRenderer;

/**
 * @Block(
 *   id = "calendar_list_block",
 *   admin_label = @Translation("Calendar list block"),
 *   category = @Translation("Calendar list block")
 * )
 */
class CalendarListBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $renderer = new ParsCalendarRenderer();

    return [
      '#type' => 'markup',
      '#markup' => $renderer->renderList(new \DateTime(), 2, \Drupal::languageManager()->getCurrentLanguage()->getId()),
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
