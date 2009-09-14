<?php

/*
 * This file is part of the swCrossLinkApplicationPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 *
 * @package    swCrossLinkApplicationPlugin
 * @author     Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * @version    SVN: $Id$
 */
class swToolboxRoutingCrossApplicationRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $routing = $event->getSubject();

    $config = sfConfig::get('app_swToolbox_cross_link_application', array());

    if(!sfContext::hasInstance() || !$routing instanceof swPatternRouting)
    {

      return;
    }

    $configuration = sfContext::getInstance()->getConfiguration();
    $env = $configuration->getEnvironment();
    $app = $configuration->getApplication();

    if(!array_key_exists('enabled', $config[$app]) || !$config[$app]['enabled'])
    {

      return;
    }

    if(!array_key_exists('load', $config[$app]) || !is_array($config[$app]['load']))
    {

      return;
    }

    foreach($config[$app]['load'] as $app_to_load => $options)
    {

      $envs = $options['env'];
      $routes = (isset($options['routes']) && is_array($options['routes'])) ? $options['routes'] : array();

      if(!array_key_exists($env, $envs))
      {

        continue;
      }

      $config_handler = new swCrossApplicationRoutingConfigHandler;
      $config_handler->setApp($app_to_load);
      $config_handler->setHost($envs[$env]);
      $config_handler->setRoutes($routes);

      $routes = $config_handler->evaluate(array(sfConfig::get('sf_apps_dir').'/'.$app_to_load.'/config/routing.yml'));

      foreach($routes as $name => $route)
      {
        $routing->appendRoute($name, $route);
      }
    }
  }
}