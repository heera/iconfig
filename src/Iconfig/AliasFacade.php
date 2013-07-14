<?php namespace Iconfig;

/**
 * [MIT Licensed](http://www.opensource.org/licenses/mit-license.php)
 * Copyright (c) 2013 Sheikh Heera
 * 
 * Implements a dynamic configuration manager for any php application.
 *  
 * Compatible with PHP 5.3.0+
 * 
 * @author Sheikh Heera <mail@heera.it>
 */

/**
 * This class gives the ability to access
 * any non-static methods statically
 *
 * Class AliasFacade
 *
 * @package Iconfig
 */
class AliasFacade {

    protected static $instance;

    /**
     * @param $method
     * @param $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
 
        return call_user_func_array(array(static::$instance, $method), $args);
    }

    public static function setInstance($ConfigInstance)
    {
        static::$instance = $ConfigInstance;
    }
}