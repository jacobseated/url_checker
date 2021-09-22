<?php
/**
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-2021_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 *  /_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_
 * 
 *  @author JacobSeated
 */

namespace new_dk\router\types;

use Exception;

/**
 * Defines a route to be used with a router
 * @package new_dk\router\types
 */
class route
{
    private $string = null;
    private $pattern = null;
    private $methods = null;
    private $post_parms = null;
    private $get_parms = null;
    private $function_handler = null;
    private $class_handler = null;

    public function __construct(array $methods = ['GET', 'HEAD'])
    {
        $this->methods = $methods;
    }

    /**
     * Set or return the route string
     * @param string|null $string 
     * @return string|$this 
     */
    public function string(string $string = null)
    {
        if (null === $string) {
            return $this->string;
        } else {
            $this->string = $string;
            return $this;
        }
    }

    /**
     * Set or return the route pattern 
     * @param string|null $pattern 
     * @return string|$this 
     */
    public function pattern(string $pattern = null)
    {
        if (null === $pattern) {
            return $this->pattern;
        } else {
            $this->pattern = $pattern;
            return $this;
        }
    }

    /**
     * Set or return the supported request methods
     * @param string|null $methods 
     * @return array|$this 
     */
    public function methods(string $methods = null)
    {
        if (null === $methods) {
            return $this->methods;
        } else {
            $this->methods = $methods;
            return $this;
        }
    }

    /**
     * Set or return the supported POST parameters
     * @param array|null $post_parms 
     * @return array|$this 
     */
    public function post_parms(array $post_parms = null)
    {
        if (null === $post_parms) {
            return $this->post_parms;
        } else {
            $this->post_parms = $post_parms;
            return $this;
        }
    }

    /**
     * Set or return the supported GET parameters
     * @param array|null $get_parms 
     * @return array|$this 
     */
    public function get_parms(array $get_parms = null)
    {
        if (null === $get_parms) {
            return $this->get_parms;
        } else {
            $this->get_parms = $get_parms;
            return $this;
        }
    }

    /**
     * Set or return the function handler
     * @param string|null $function_handler 
     * @return string|$this 
     */
    public function function_handler(string $function_handler = null)
    {
        if (null === $function_handler) {
            return $this->function_handler;
        } else {
            $this->function_handler = $function_handler;
            return $this;
        }
    }

    /**
     * Set or return the class handler
     * @param string|null $class_handler 
     * @return string|$this 
     */
    public function class_handler(string $class_handler = null)
    {
        if (null === $class_handler) {
            return $this->class_handler;
        } else {
            $this->class_handler = $class_handler;
            return $this;
        }
    }
}
