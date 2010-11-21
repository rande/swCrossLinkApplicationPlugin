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
class swEncapsulateRoute extends sfRoute implements Serializable
{

  protected
    $route
  ;


  /**
   * Constructor.
   *
   * Available options:
   *
   *  * variable_prefixes:                An array of characters that starts a variable name (: by default)
   *  * segment_separators:               An array of allowed characters for segment separators (/ and . by default)
   *  * variable_regex:                   A regex that match a valid variable name ([\w\d_]+ by default)
   *  * generate_shortest_url:            Whether to generate the shortest URL possible (true by default)
   *  * extra_parameters_as_query_string: Whether to generate extra parameters as a query string
   *
   * @param string $pattern       The pattern to match
   * @param array  $defaults      An array of default parameter values
   * @param array  $requirements  An array of requirements for parameters (regexes)
   * @param array  $options       An array of options
   */
  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
    $this->pattern      = $pattern;
    $this->defaults     = $defaults;
    $this->requirements = $requirements;
    $this->options      = $options;

    $this->initCrossRouting();
  }

  public function initCrossRouting()
  {
    // pattern is only null when autoregister is on
    if($this->pattern == null) {
      return;
    }

    $route = swPatternRouting::getCrossApplicationRouteInstance($this->options['encapsulated']['name']);
    $this->setRoute($route->getRoute());
  }

  public function setRoute(sfRoute $route)
  {
    $this->route = $route;
  }

  public function getRoute()
  {
    return $this->route;
  }

  public function compile()
  {
    if ($this->compiled)
    {
      return;
    }

    $this->compiled = true;
  }

  public function __call($method, $arguments)
  {
    return call_user_func_array(array($this->route, $method), $arguments);
  }

  public function serialize()
  {

    return serialize(array($this->route));
  }

  public function unserialize($data)
  {
    list($this->route) = unserialize($data);
  }

  public function generate($params, $context = array(), $absolute = false)
  {

    unset(
      $params['sw_app']
    );

    $url = $this->route->generate($params, $context, true);


    $requirements = $this->route->getRequirements();

    if ( isset($requirements['sw_host']) && $requirements['sw_host'] != $context['host'])
    {
      // apply the required host
      $protocol = $context['is_secure'] ? 'https' : 'http';
      $url = $protocol.'://'.$requirements['sw_host'].$url;
    }

    return $url;
  }

  public function isBound()
  {

    return $this->route->isBound();
  }

  public function bind($context, $parameters)
  {

    return $this->route->bind($context, $parameters);
  }

  public function matchesUrl($url, $context = array())
  {

    // always return false to not match current application routes

    return false;
  }

  public function matchesParameters($params, $context = array())
  {
    // always return false to not match current application routes

    return false;
  }

  public function getPattern()
  {

    return $this->route->getPattern();
  }

  public function getRegex()
  {

    return $this->route->getRegex();
  }

  public function getTokens()
  {

    return $this->route->getTokens();
  }

  public function getOptions()
  {

    return $this->route->getOptions();
  }

  public function getVariables()
  {

    return $this->route->getVariables();
  }

  public function getDefaults()
  {

    return $this->route->getDefaults();
  }


  public function getRequirements()
  {

    return $this->route->getRequirements();
  }

  public function getDefaultParameters()
  {

    return $this->route->getDefaultParameters();
  }

  public function setDefaultParameters($parameters)
  {

    return $this->route->setDefaultParameters($parameters);
  }

  public function getDefaultOptions()
  {

    return $this->route->getDefaultOptions();
  }

  public function setDefaultOptions($options)
  {

    return $this->route->setDefaultOptions($options);
  }
}