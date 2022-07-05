<?php

namespace Drupal\login_switch\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AddNoIndexHeader implements EventSubscriberInterface {

  protected $configFactory;

  /**
   * AddNoIndexHeader constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   ConfigFactoryInterface.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onRespond', -100];
    return $events;
  }


  /**
  * Add noindex Headers
  *
  * @param FilterResponseEvent $event
  */
 public function onRespond(FilterResponseEvent $event)
 {
   if (!$event->isMasterRequest()) {
     return;
   }

   // Get Configuration & Event Response
   $config = $this->configFactory->get('login_switch.settings');
   $response = $event->getResponse();

   // Routes to check for added header
   $routes = [
       'login' => 'user.login',
       'register' => 'user.register',
       'password' => 'user.pass',
     ];

     $route = \Drupal::routeMatch()->getRouteName();
     if(in_array($route, $routes)) {
       $key = array_search($route, $routes);
       if(!empty($key)) {
         if($config->get($key . '_noindex')) {
           $response->headers->set('X-Robots-Tag', 'noindex');
         }
       }
     }
   }
 }
