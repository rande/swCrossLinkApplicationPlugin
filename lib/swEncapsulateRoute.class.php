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
class swEncapsulateRoute extends sfRoute implements Serializable
{

  protected
    $route,
    $host,
    $app;


  public function __construct(sfRoute $route)
  {
    $this->route = $route;
  }

  public function __call($method, $arguments)
  {
    return call_user_func_array(array($this->route, $method), $arguments);
  }


  public function serialize()
  {
    
    return serialize(array($this->route, $this->host, $this->app));
  }

  public function unserialize($data)
  {
    list($this->route, $this->host, $this->app) = unserialize($data);
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

    return $this->route->matchesParameters($params, $context = array());
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