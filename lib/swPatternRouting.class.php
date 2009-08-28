<?php

/*
 *  $Id$
 *
 * (c) 2008 Thomas Rabaix <thomas.rabaix@soleoweb.com>
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.soleoweb.com>.
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
      $url = $this->cache->get('symfony.routing.data.'.$cacheKey);
      
      if ($this->options['lookup_cache_dedicated_keys'] && $url)
      {
        
        return strpos($name, '.') ? $url : $this->fixGeneratedUrl($url, $absolute);
      }
      elseif (isset($this->cacheData[$cacheKey]))
      {

        return strpos($name, '.') ? $url : $this->fixGeneratedUrl($this->cacheData[$cacheKey], $absolute);
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