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
class swPatternRouting extends sfPatternRouting
{

  protected
    $context_routes = null;

  /**
   * overwrite the sfPatternRouting to handle the cross link application feature
   *
   * does not call sfPatternRouting::fixGeneratedUrl if the route is an application link
   *
   * @see sfPatternRouting#generate($name, $params, $absolute)
   */
  public function generate($name, $params = array(), $absolute = false)
  {

   // fetch from cache
    if (!is_null($this->cache))
    {
      $cacheKey = 'generate_'.$name.'_'.md5(serialize(array_merge($this->defaultParameters, $params))).'_'.md5(serialize($this->options['context']));

      if ($this->options['lookup_cache_dedicated_keys'] && $url = $this->cache->get('symfony.routing.data.'.$cacheKey))
      {

        return strpos($name, '.') ? $url : $this->fixGeneratedUrl($url, $absolute);
      }
      elseif (isset($this->cacheData[$cacheKey]))
      {

        return strpos($name, '.') ? $this->cacheData[$cacheKey] : $this->fixGeneratedUrl($this->cacheData[$cacheKey], $absolute);
      }
      
    }

    if ($name)
    {

      // named route
      if (!isset($this->routes[$name]))
      {
        // try to find the route on different application
        if(($application_name = $this->getApplicationRoute($name)) === false)
        {
          throw new sfConfigurationException(sprintf('The route "%s" does not exist.', $name));
        };

        $name = $application_name;
      }

      $route = $this->routes[$name];

      if (is_string($route))
      {
        $route = $this->loadRoute($name);
      }
      $route->setDefaultParameters($this->defaultParameters);
    }
    else
    {
      // find a matching route
      if (false === $route = $this->getRouteThatMatchesParameters($params, $this->options['context']))
      {
        throw new sfConfigurationException(sprintf('Unable to find a matching route to generate url for params "%s".', is_object($params) ? 'Object('.get_class($params).')' : str_replace("\n", '', var_export($params, true))));
      }
    }

    $url = $route->generate($params, $this->options['context'], $absolute);

    // store in cache
    if (!is_null($this->cache))
    {
      if ($this->options['lookup_cache_dedicated_keys'])
      {
        $this->cache->set('symfony.routing.data.'.$cacheKey, $url);
      }
      else
      {
        $this->cacheChanged = true;
        $this->cacheData[$cacheKey] = $url;
      }
    }

    return strpos($name, '.') ? $url : $this->fixGeneratedUrl($url, $absolute);
  }

  protected function getApplicationRoute($route_name)
  {
    if(is_null($this->context_routes))
    {
      $this->context_route = array();
      foreach(array_keys($this->routes) as $name)
      {
        if(($pos = strpos($name, '.')) === false)
        {
          continue;
        }

        $this->context_routes[substr($name, $pos + 1)] = $name;
      }
    }

    if(!is_array($this->context_routes))
    {

      return false;
    }

    if(!array_key_exists($route_name, $this->context_routes))
    {

      return false;
    }

    return $this->context_routes[$route_name];
  }
}