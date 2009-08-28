<?php

/*
 * This file is part of the swCrossLinkApplicationPlugin package.
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class swCrossLinkApplicationPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    if($this->configuration instanceof sfApplicationConfiguration)
    {
      // Cross link application
      $cla = sfConfig::get('app_swToolbox_cross_link_application', array());

      if (array_key_exists($this->configuration->getApplication(), $cla) && $cla[$this->configuration->getApplication()]['enabled'])
      {
        $this->dispatcher->connect('routing.load_configuration', array('swToolboxRoutingCrossApplicationRouting', 'listenToRoutingLoadConfigurationEvent'));
      }
    }
  }
}