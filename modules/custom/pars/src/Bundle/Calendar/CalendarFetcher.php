<?php

namespace Drupal\pars\Bundle\Calendar;

use DateInterval;
use DatePeriod;
use DateTime;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\node\Entity\Node;

class CalendarFetcher {

  /**
   * @param DateTime $from
   * @param DateTime $to
   * @return array{
   *  nid: integer,
   *  title: string,
   *  date: DateTime,
   *  from: DateTime,
   *  to: DateTime,
   *  link: string,
   * }
   */
  public function fetchEvents(DateTime $from, DateTime $to): array {
    $monthStart = new DrupalDateTime($from->format('Y-m-d'));
    $monthEnd = new DrupalDateTime($to->format('Y-m-d'));
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'event', '=');
    $query->condition('status', 1);

    $or = $query->orConditionGroup();

    // start before month start, end after month end
    $or->condition($query->andConditionGroup()
      ->condition('field_event_start', $monthStart->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<=')
      ->condition('field_event_end', $monthEnd->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>'));

    // start after month start, end after month end
    $or->condition($query->andConditionGroup()
      ->condition('field_event_start', $monthStart->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>')
      ->condition('field_event_start', $monthEnd->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<=')
      ->condition('field_event_end', $monthEnd->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>'));

    // start before month start, end before month end
    $or->condition($query->andConditionGroup()
      ->condition('field_event_start', $monthStart->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<')
      ->condition('field_event_end', $monthStart->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=')
      ->condition('field_event_end', $monthEnd->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<'));

    // start after month start, end before month end
    $or->condition($query->andConditionGroup()
      ->condition('field_event_start', $monthStart->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>')
      ->condition('field_event_end', $monthEnd->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<'));

    $query->condition($or);

    $query->sort('created');
    $nids =  $query->execute();
    $nodes =  Node::loadMultiple($nids);

    $result = [];
    foreach ($nodes as $node) {
      $startDate = new DateTime($node->get('field_event_start')->value);
      $endDate = new DateTime($node->get('field_event_end')->value);

      $interval = DateInterval::createFromDateString('1 day');
      $period = new DatePeriod($startDate, $interval, $endDate);

      foreach ($period as $dt) {
        $result[] = [
          'nid' => $node->id(),
          'title' => $node->getTitle(),
          'date' => new DateTime($dt->format('Y-m-d')),
          'link' => $node->toUrl()->toString(),
          'from' => $startDate,
          'to' => $endDate,
        ];
      }


    }

    return $result;
  }
}
