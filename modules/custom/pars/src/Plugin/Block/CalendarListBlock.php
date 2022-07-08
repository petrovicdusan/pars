<?php

namespace Drupal\pars\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Markup;
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
    $content = $renderer->renderList(new \DateTime(), 2, \Drupal::languageManager()->getCurrentLanguage()->getId());

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
