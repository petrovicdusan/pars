<?php

namespace Drupal\pars\Bundle\Calendar;

/**
 * Calendar build helper. Mostly useful for dynamically class addition
 */
class ParsCalendar {

  private $calendar = '';
  private $language;

  public function __construct($language) {
    $this->language = $language;
    $this->calendar = "<table id='pars-calendar-block' lang='" . $language . "'>";
  }

  /**
   * Adds Calendar title
   */
  public function addHeader($value, $goto = '') {
    if (!empty($goto)) {
      $date_prev = date('m-Y', strtotime($goto . ' -1 month'));
      $date_next = date('m-Y', strtotime($goto . ' +1 month'));

      $this->calendar .= "<thead>
									<tr>
										<th class='nav'>
                      <a href='#' data-goto='{$date_prev}' data-lang='{$this->language}'>
                        <div class='calendar-nav calendar-nav-left'></div>
                      </a>
                    </th>
										<th class='pars-calendar-block-title' colspan=5>{$value}</th>
										<th class='nav'><a href='#' data-goto='{$date_next}' data-lang='{$this->language}'>
                      <div class='calendar-nav calendar-nav-right'></div></a>
                    </th>
                  </tr>
								</thead>";
    } else {
      $this->calendar .= '<tbody>';
      $this->calendar .= "<tr><th class='pars-calendar-block-title' colspan=7>{$value}</th></tr>";
    }
    $mon            = mb_substr(t('Monday', [], ['langcode' => $this->language]), 0, 3);
    $tue            = mb_substr(t('Tuesday', [], ['langcode' => $this->language]), 0, 3);
    $wed            = mb_substr(t('Wednesday', [], ['langcode' => $this->language]), 0, 3);
    $thu            = mb_substr(t('Thursday', [], ['langcode' => $this->language]), 0, 3);
    $fri            = mb_substr(t('Friday', [], ['langcode' => $this->language]), 0, 3);
    $sat            = mb_substr(t('Saturday', [], ['langcode' => $this->language]), 0, 3);
    $sun            = mb_substr(t('Sunday', [], ['langcode' => $this->language]), 0, 3);
    $this->calendar .= "<tr>
								<td class='day-title-cell' data-name='" . $mon . "'>" . $mon . "</td>
								<td class='day-title-cell' data-name='" . $tue . "'>" . $tue . "</td>
								<td class='day-title-cell' data-name='" . $wed . "'>" . $wed . "</td>
								<td class='day-title-cell' data-name='" . $thu . "'>" . $thu . "</td>
								<td class='day-title-cell' data-name='" . $fri . "'>" . $fri . "</td>
								<td class='day-title-cell' data-name='" . $sat . "'>" . $sat . "</td>
								<td class='day-title-cell day-cell-right' data-name='" . $sun . "'>" . $sun . "</td>
							</tr>";
  }

  public function openRowTag() {
    $this->calendar .= '<tr>';
  }

  public function closeRowTag() {
    $this->calendar .= '</tr>';
  }

  /**
   * Adds <td> to table adn sets all of ot's data
   */
  public function addCell($value, $class = [], $link = [], $day_hover_data = [], $language = []) {
    $classes   = '';
    $data_date = '';
    $dat       = '';
    if (!empty($link)) {
      if ($language == 'sr') $page_url = 'календар-догађаја';
      elseif ($language == 'sr-lat') $page_url = 'kalendar-dogadjaja';
      elseif ($language == 'en') $page_url = 'event-calendar';
      $day   = $link['day_num'];
      $month = $link['month'];
      $year  = $link['year'];
    }
    if (!empty($class)) {
      $classes = 'class="' . implode(' ', $class) . '"';
    }

    if (!empty($day_hover_data)) {
      $list = '<div class="pars-tooltip">
						<ul>';
      foreach ($day_hover_data as $data) {
        if (!empty($link)) {
          $nid      = $data['nid'];
          $link_url = '/' . $page_url . '?d=' . $day . '&m=' . $month . '&y=' . $year . '&e=' . $nid . '#' . $day . '-' . $nid;
        } else {
          $link_url = "";
        }

        $list .=
          '<li class="pars-tooltip-list-element"><a href="' . $link_url . '"><div>'
          . strlen($data['title']) > 50 ? substr($data['title'], 0, 50) . "..." : $data['title']
            . '</div></a></li>';
      }
      $list  .= '</ul></div>';
      $value .= $list;
    }
    $this->calendar .= '<td ' . $classes . '><div class="table-cell-inside-div" ' . $data_date . ' >' . $value . '</div></td>';
  }

  public function finish() {
    $this->calendar .= '</tbody></table>';
    return $this->calendar;
  }

  private function getData($year, $month) {
//    $month = !empty($variables['month']) ? $variables['month'] : date('m', time());
//    $year  = !empty($variables['year']) ? $variables['year'] : date('Y', time());

    $date_field_name_from = 'field_' . MS_CALENDAR_CALENDAR_DATE_FIELD_NAME . '_value';
    $date_field_name_to   = 'field_' . MS_CALENDAR_CALENDAR_DATE_FIELD_NAME . '_value2';

    $query = db_select('node', 'n')->fields('n', ['nid', 'title', 'created']);
    $query->condition('n.status', 1)->condition('n.language', $variables['language']);
    $query->condition('n.type', '%' . db_like('event') . '%', 'LIKE');

    $res    = $query->execute();
    $events = [];

    while ($row = $res->fetchObject()) {
      $q = db_select(MS_CALENDAR_CALENDAR_DATE_FIELD_TABLE, 't')->fields('t', [$date_field_name_from, $date_field_name_to])->condition('entity_id', $row->nid);

      $day_from   = intval(format_date($row->created, 'custom', 'd'));
      $month_from = intval(format_date($row->created, 'custom', 'm'));
      $year_from  = intval(format_date($row->created, 'custom', 'Y'));
      $day_to     = intval(format_date($row->created, 'custom', 'd'));
      $month_to   = intval(format_date($row->created, 'custom', 'm'));
      $year_to    = intval(format_date($row->created, 'custom', 'Y'));
      $r          = $q->execute();
      if ($rw = $r->fetchObject()) {
        $day_from   = intval(format_date(strtotime($rw->{$date_field_name_from}), 'custom', 'd'));
        $month_from = intval(format_date(strtotime($rw->{$date_field_name_from}), 'custom', 'm'));
        $year_from  = intval(format_date(strtotime($rw->{$date_field_name_from}), 'custom', 'Y'));
        $day_to     = intval(format_date(strtotime($rw->{$date_field_name_to}), 'custom', 'd'));
        $month_to   = intval(format_date(strtotime($rw->{$date_field_name_to}), 'custom', 'm'));
        $year_to    = intval(format_date(strtotime($rw->{$date_field_name_to}), 'custom', 'Y'));
      }
      if ($month != $month_from || $year != $year_from || $month != $month_to || $year != $year_to) continue;
      if (!empty($day) && !($day > $day_from && $day < $day_to)) continue;
      for ($ii = $day_from; $ii <= $day_to; $ii++) {
        $events[] = [
          'title' => $row->title,
          'nid'   => $row->nid,
          'day'   => $ii,
        ];
      }
    }

    $partSorted = [];
    foreach ($events as $event) {
      $partSorted[$event['day']][$event['nid']] = $event['title'];
    }

    $eventsSorted = [];
    foreach ($partSorted as $day => $nidArray) {
      foreach ($nidArray as $nid => $title) {
        $eventsSorted[] = [
          'title' => $title,
          'nid'   => $nid,
          'day'   => $day,
        ];
      }
    }
    return $eventsSorted;
  }

}
