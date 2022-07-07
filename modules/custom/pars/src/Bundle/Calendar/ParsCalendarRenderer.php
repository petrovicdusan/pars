<?php

namespace Drupal\pars\Bundle\Calendar;

use DateInterval;
use DateTime;
use Drupal\Core\Language\Language;

class ParsCalendarRenderer {

  /**
   * @param integer|null $month
   * @param integer|null $year
   * @param string|null $language
   * @return string
   * @throws \Exception
   */
  public function render(int $month = null, int $year = null, string $language = null): string {
    $language = $language ?? \Drupal::languageManager()->getCurrentLanguage()->getId();

    //Get date
    if(!isset($month) || !isset($year) ) {
      $date = time();
    }
    else {
      $date = strtotime('01-' . $month . '-' . $year);
    }

    //This puts the day, month, and year in seperate variables
    $month = date('m', $date);
    $year = date('Y', $date);
    $today_day = date('d', time());
    $today_month = date('m', time());
    $today_year = date('Y', time());
    //Here we generate the first day of the month
    $first_day = mktime(0, 0, 0, $month, 1, $year);

    //This gets us the month name
    $title = t(date('F', $first_day), [], ['langcode' => $language, 'context' => 'Long month name']);

    //Here we find out what day of the week the first day of the month falls on
    $day_of_week = date('D', $first_day);

    //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
    switch($day_of_week) {
      case "Mon": $blank = 0; break;
      case "Tue": $blank = 1; break;
      case "Wed": $blank = 2; break;
      case "Thu": $blank = 3; break;
      case "Fri": $blank = 4; break;
      case "Sat": $blank = 5; break;
      case "Sun": $blank = 6; break;
    }

    //We then determine how many days are in the current month
    $days_in_month = cal_days_in_month(0, $month, $year) ;

    //Here we start building the table heads
    $calendar = new ParsCalendar($language);

    $calendar->addHeader('<div>' . $title . ' ' . $year . '</div>', '1-' . intVal($month) . '-' . intval($year));
    //This counts the days in the week, up to 7
    $day_count = 1;
    $calendar->openRowTag();

    if($month == 1){
      $days_in_prew_month = cal_days_in_month(0, 12, $year-1) ;
    }else{
      $days_in_prew_month = cal_days_in_month(0, $month-1, $year) ;
    }
    //first we take care of those blank days
    while ($blank > 0) {
      $calendar->addCell($days_in_prew_month - $blank + 1, array('day-prew-month', 'not-this-month'));
      $blank = $blank - 1;
      $day_count ++;
    }

    //sets the first day of the month to 1
    $day_num = 1;

    //Get nodes that are set to appear in calendar in this month
    $fetcher = new CalendarFetcher();
    $monthStart = new DateTime("{$year}-{$month}-01");
    $monthEnd = new DateTime(date("Y-m-t", strtotime("{$year}-{$month}-01")));
    $events = $fetcher->fetchEvents($monthStart, $monthEnd);
    //count up the days, until we've done all of them in the month
    while ($day_num <= $days_in_month) {
      $classes  = array('day-cell');
      if ($day_num == $today_day && $today_month == $month && $today_year == $year) $classes[] = 'today-day-class';

      if($day_count >= 7) {
        $classes[] = 'day-cell-right';
      }

      $day_hover_data = array();

      foreach($events as $event) {
        /** @var DateTime $date */
        $date = $event['date'];
        if($day_num == $date->format('d')) {
          $classes[] = 'day-cell-event';
          $day_hover_data[] = array(
            'nid' => $event['nid'],
            'link' => $event['link'],
            'title' => $event['title'],
            'from' => $event['from'],
            'to' => $event['to'],
            'date' => $event['date'],
          );
        }
      }
      $calendar->addCell($day_num, $classes, $day_hover_data, $language);
      $day_num++;
      $day_count++;

      //Make sure we start a new row every week
      if ($day_count > 7) {
        $calendar->closeRowTag();
        $calendar->openRowTag();
        $day_count = 1;
      }
    }

    //Finaly we finish out the table with some blank details if needed
    $day_next_month = 1;
    while ($day_count > 1 && $day_count <= 7) {
      $classes = array('day-cell');
      $classes[] = 'day-next-month';
      $classes[] = 'not-this-month';
      if($day_count >= 7) {
        $classes[] = 'day-cell-right';
      }
      $calendar->addCell($day_next_month, $classes);
      $day_next_month++;
      $day_count++;
    }
    $calendar->closeRowTag();

    return $calendar->finish();
  }

  /**
   * Render list of events for next $weekNum weeks
   *
   * @param DateTime $dateFrom
   * @param integer $weekNum
   * @param string|null $language
   * @return string
   */
  public function renderList(DateTime $dateFrom, int $weekNum, string $language = null): string {
    $fetcher = new CalendarFetcher();
    $dateTo = clone $dateFrom;
    $dateTo->modify("+ {$weekNum} week");

    $t = 't';
    $data = [];
    $events  = $fetcher->fetchEvents($dateFrom, $dateTo);
    foreach ($events as $event) {
      $data[$event['nid']] = $event;
    }

    $result = <<<EOT
      <table class="calendar-table table mt-2">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">{$t('Title')}</th>
            <th scope="col">{$t('From')}</th>
            <th scope="col">{$t('To')}</th>
          </tr>
        </thead>
        <tbody>
EOT;

    foreach (array_values($data) as $key => $ev) {
      $rowNum = $key + 1;
      $result .= <<<EOT
        <tr>
          <th scope="row">{$rowNum}</th>
          <td>{$ev['title']}</td>
          <td>{$ev['from']->format('d.m.Y.')}</td>
          <td>{$ev['to']->format('d.m.Y.')}</td>
        </tr>
EOT;
    }
    $result .= "</tbody></table>";

    return $result;
  }
}
