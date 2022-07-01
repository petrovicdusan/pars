<?php

namespace Drupal\pars\Bundle\Calendar;

use Drupal\Core\Language\Language;

class ParsCalendarRenderer {

  /**
   * @param $variables
   * @return string
   */
  public function render($variables = []) {
    $day_hover_data = array();
    $variables['language'] = array_key_exists('language', $variables) ? $variables['language'] : \Drupal::languageManager()->getCurrentLanguage()->getId();

    //Get date
    if(empty($variables['month']) || empty($variables['year']) ) {
      $date = time();
    }
    else {
      $date = strtotime('01-' . $variables['month'] . '-' . $variables['year']);
    }

    //This puts the day, month, and year in seperate variables
    $day = date('d', $date);
    $month = date('m', $date);
    $year = date('Y', $date);
    $today_day = date('d', time());
    $today_month = date('m', time());
    $today_year = date('Y', time());
    //Here we generate the first day of the month
    $first_day = mktime(0, 0, 0, $month, 1, $year);

    //This gets us the month name
    $title = date('F', $first_day);

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
    $calendar = new ParsCalendar($variables['language']);

    //$calendar->addHeader('<a data-goto="'. $month . '-' . $year .'" href="' . '/' . $variables['language'] . '/' . locale('calendar', NULL, $variables['language']) . '/' . $month . '/' . $year . '">' . locale($title, NULL, $variables['language']) . ' ' . $year . '</a>', '1-' . intVal($month) . '-' . intval($year));
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
//    $events = _pars_calendar_get_events($variables);
    $events = [];
    //count up the days, untill we've done all of them in the month
    while ($day_num <= $days_in_month) {
      $classes  = array('day-cell');
      $as_link = array();
      if ($day_num == $today_day && $today_month == $month && $today_year == $year) $classes[] = 'today-day-class';

      if($day_count >= 7) {
        $classes[] = 'day-cell-right';
      }

      $day_hover_data = array();

      foreach($events as $event) {
        if($day_num == $event['day']) {
          $nid = $event['nid'];
          $day_hover_data[] = array(
            'nid' => $nid,
            'title' => $event['title']
          );
        }
        if($day_num == $event['day'] && (!in_array('day-cell-event-rnids-event', $classes) || !in_array('day-cell-event-other-event', $classes))) {
          $classes[] = 'day-cell-event';
//          $node = node_load($nid);
          $node = (object)[];
          $as_link = array(
            //'href' => 'calendar/' . $day_num . '/' . $month . '/' . $year,
            'day_num' => $day_num,
            'post_date' => $node->created,
            'month' => $month,
            'year' => $year,
          );
        }
      }
      $calendar->addCell($day_num, $classes, $as_link, $day_hover_data, $variables['language']);
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
}