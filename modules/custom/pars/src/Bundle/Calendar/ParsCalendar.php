<?php

namespace Drupal\pars\Bundle\Calendar;

use DateTime;

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
                        <div class='calendar-nav calendar-nav-left fas fa-angle-left'></div>
                      </a>
                    </th>
										<th class='pars-calendar-block-title' colspan=5>{$value}</th>
										<th class='nav'><a href='#' data-goto='{$date_next}' data-lang='{$this->language}'>
                      <div class='calendar-nav calendar-nav-right fas fa-angle-right'></div></a>
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
  public function addCell($value, $classArr = [], $day_hover_data = [], $language = []) {
    $classes = !empty($classArr) ? 'class="' . implode(' ', $classArr) . '"' : '';

    if (!empty($day_hover_data)) {
      $list = '<div class="pars-tooltip"><ul>';
      foreach ($day_hover_data as $data) {
        $link  = $data['link'];
        $titleLabel = t('Title');
        $fromLabel = t('From');
        $toLabel = t('To');
        $dateLabel = t('Date');

        $title = strlen($data['title']) > 50 ? substr($data['title'], 0, 50) . "..." : $data['title'];
        /** @var DateTime $from */
        $from = $data['from'];
        /** @var DateTime $to */
        $to = $data['to'];
        /** @var DateTime $date */
        $date = $data['date'];
        $list  .= "<li class='pars-tooltip-list-element'>
                    <a href='{$link}' target='_blank'>
                        <span class='title'><strong>{$titleLabel}</strong>: {$title}</span>
                        <span class='date'><strong>{$dateLabel}</strong>: {$date->format('d.m.Y.')}</span>
                        <span class='from'><strong>{$fromLabel}</strong>: {$from->format('d.m.Y.')}</span>
                        <span class='to'><strong>{$toLabel}</strong>: {$to->format('d.m.Y.')}</span>
                    </a>
                  </li>";
      }
      $list  .= '</ul></div>';
      $value .= $list;
    }
    $this->calendar .= "<td {$classes}><div class='table-cell-inside-div'>{$value}</div></td>";
  }

  public function finish() {
    $this->calendar .= '</tbody></table>';
    return $this->calendar;
  }

}
