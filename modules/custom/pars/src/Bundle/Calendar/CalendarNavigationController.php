<?php

namespace Drupal\pars\Bundle\Calendar;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class CalendarNavigationController extends ControllerBase {

  /**
   * Run cron jobs.
   */
  public function run() {
    $request = \Drupal::request();
    $date = trim(stripcslashes($request->get('date')));
    $date = explode('-', $date);
    $language = trim(stripcslashes($request->get('language')));
    $renderer = new ParsCalendarRenderer();
    $variables = [
      'month'    => intval($date[0]),
      'year'     => intval($date[1]),
      'language' => $language,
    ];

    return new JsonResponse(['calendar' => $renderer->render($variables), 'status'=> 200]);
  }
}
