<?php

namespace Drupal\pars\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;
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
    $content = $renderer->render(null, null, \Drupal::languageManager()->getCurrentLanguage()->getId());

    return [
      '#type' => 'markup',
      '#markup' => Markup::create($content),
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
