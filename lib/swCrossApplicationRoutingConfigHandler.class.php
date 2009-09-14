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
class swCrossApplicationRoutingConfigHandler extends sfRoutingConfigHandler
{

  protected
    $app,
    $host,
    $routes;

  public function setApp($app)
  {
    $this->app = $app;
  }

  public function getApp()
  {
    return $this->app;
  }

  public function setHost($host)
  {
    $this->host = $host;
  }

  public function getHost()
  {
    return $this->host;
  }

  public function setRoutes($routes)
  {
    $this->routes = $routes;
  }

  public function getRoutes()
  {
    return $this->routes;
  }

  protected function parse($configFiles)
  {
    $routes = parent::parse($configFiles);

    $new_routes = array();
    foreach($routes as $name => $route)
    {
      $name = $this->app.'.'.$name;
      $new_routes[$name] = $route;
      $new_routes[$name][1][2]['sw_app'] = $this->app;
      $new_routes[$name][1][2]['sw_host'] = $this->host;
    }

    return $new_routes;
  }

  public function getOriginalName($name)
  {

    return substr($name, strpos($name, '.') + 1);
  }

  public function evaluate($configFiles)
  {
    $routeDefinitions = $this->parse($configFiles);

    $routes = array();
    $limit  = count($this->routes) > 0;

    foreach ($routeDefinitions as $name => $route)
    {


      // we only load routes defined in the app.yml from
      if($limit && !in_array($this->getOriginalName($name), $this->routes))
      {

        continue;
      }

      $r = new ReflectionClass($route[0]);

      if($r->isSubclassOf('sfRouteCollection'))
      {

        $route[1][0]['requirements']['sw_app'] = $this->app;
        $route[1][0]['requirements']['sw_host'] = $this->host;

        $collection_route = $r->newInstanceArgs($route[1]);

        foreach($collection_route->getRoutes() as $name => $route)
        {
          $routes[$this->app.'.'.$name] = new swEncapsulateRoute($route, $this->host, $this->app);;
        }
      }
      else
      {
        $route[1][2]['sw_app'] = $this->app;
        $route[1][2]['sw_host'] = $this->host;

        $routes[$name] = new swEncapsulateRoute($r->newInstanceArgs($route[1]), $this->host, $this->app);
      }
    }

    return $routes;
  }
}