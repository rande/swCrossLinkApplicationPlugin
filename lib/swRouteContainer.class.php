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
class swRouteContainer extends swPatternRouting
{

  protected $routes = array();

  public function __construct() {
    // nothing to do
  }

  public function appendRoute($name, $route) {

    $this->routes[$name] = $route;
  }

  public function getRoute($name) {
    return $this->routes[$name];
  }
}