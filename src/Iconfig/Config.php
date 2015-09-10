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
 * This class gives the ability to manipulate
 * the configurations for an application.
 *
 *
 * Class Confog
 *
 * @package IConfig
 */

class Config {
	
	protected $found = false;
	protected $currentItem = [];
	protected $commonSettings = [];
	
	/**
	* 
	* Cconstructor to init the class ans optionally set alias
	*
	* @param string $path [Path to load configuration from]
	* @param string $alias (optional if user provide an alias)
	* @return void
	* 
	*/
	public function __construct($path = null, $alias = null)
	{
		if(is_null($path)) {
			throw new \Exception("Path required to load configuration files from");
		}
		
		if(!count($this->commonSettings)) {
			$this->load($path);
		}

		if($alias) {
			AliasFacade::setInstance($this);
			class_alias('\Iconfig\AliasFacade', $alias);
		}
	}

	/**
	 * __call required for set and get methods
	 * @param  String $method [name of the calling method]
	 * @param  Array $params [Method's parameters]
	 * @return Mixed/Nothing
	 */
	public function __call($method, $params = null)
	{
		try{
			$methodPrefix = substr($method, 0, 3);
			$methodName = substr($method, 3);

			// This part will set properties using set
			if($methodPrefix=='set'){
				if(count($params) < 2) {
					$message = "Invalid parameter(s) given, method <strong>$method</strong> requires 2";
					$message .= " parameter(s),  but " . count($params) . " given!";
					throw new \Exception($message);
				}

				$key = strtolower($methodName).'.'.$params[0];
				$value = $params[1];
				$this->array_set($this->commonSettings, $key, $value);
				return $this;
			}

			// This part will return properties using get
			elseif($methodPrefix=='get'){
				
				// Set default value to return when no properties found
				$default = count($params) === 2 && !is_callable($params[1])
				? $params[1]
				: ( count($params) === 2 && $params[0] === '' ? strtolower($methodName) : null );

				if($params==null){
					$key = strtolower($methodName);
					if($key === 'all') $result = $this->commonSettings;
					else $result = $this->array_get($this->commonSettings, $key);
				}
				else {
					$key = strtolower($methodName);
					$key .= isset($params[0]) ? '.' . $params[0] : '';

					// If a closure is given in 2nd argumet with getMethod(), call it
					$result = $this->array_get($this->commonSettings, $key, $default);
					if(isset($params[1]) && is_callable($params[1])) {
						return $params[1]($result);
					}
				}
				return $result;
			}
			else{
				throw new \Exception("Undefined method <strong>$method</strong> has been called!");
			}
		}
		catch(\Exception $e){
			exit($e->getMessage());
		}
	}
	
	
	/**
	 * Loads all configurations from application/config/ php files
	 * @param  String $path 
	 * @return Array
	 */
	public function load($path)
	{
		$dir = new \DirectoryIterator(realpath($path));
		foreach ($dir as $fileInfo) {
			if($fileInfo->isFile() && $fileInfo->isReadable() && $fileInfo->getExtension()=='php') {
				$array = include $fileInfo->getPathname();
				$fileName = $fileInfo->getFilename();
				$keyName = substr($fileName, 0, strpos($fileName, '.'));
				$this->commonSettings[$keyName] = $array;
			}
		}
	}

	
	
	/** Helper functions **/
	
	/** Get an item from an array using "dot" notation.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */

	private function array_get($array, $key, $default = null)
	{
		if (is_null($key)) return $array;
		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) or ! array_key_exists($segment, $array))
			{
				return $default;
			}
			$array = $array[$segment] ?: $default;
		}
		return $array;
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	private function array_set(&$array, $key, $value)
	{
		if (is_null($key)) return $array = $value;

		$keys = explode('.', $key);
		while (count($keys) > 1)
		{
			$key = array_shift($keys);
			if ( ! isset($array[$key]) or ! is_array($array[$key]))
			{
				$array[$key] = array();
			}
			$array =& $array[$key];
		}
		$array[array_shift($keys)] = $value;
	}

	/**
	* Recursively finds an item from the commonSettings array 
	*
	* @param  string   $item
	* @param  array   $array
	* @return object/$this
	*/
	public function find($item = null, $array = null)
	{
		if(is_null($item)) return null;
		//$this->found = false;
		
		if(strpos($item, '.')) {
			$arr = explode('.', $item);
			if(count($arr) > 2 ) {
				$itemToSearch = join('.', array_slice($arr, 1));
			}
			else {
				$itemToSearch = $arr[1];
			}
			return $this->findItemIn($itemToSearch, $arr[0]);
		}
		else {
			$array = !is_null($array) ? $array : $this->commonSettings;
			foreach ($array as $key => $value) {
				if($key === $item) {
					$this->currentItem = $value;
					$this->found = true;
					break;
				}
				else {
					if(is_array($value)) {
						$this->find($item, $value);
					}
				}
			}
		}
		
		if(!$this->found) {
			return false;
		}
		return $this->currentItem;
	}


	/**
	* Recursively finds an item from the commonSettings array 
	*
	* @param  string   $item
	* @param  array   $array
	* @return object/$this
	*/
	private function findItemIn($item, $key)
	{
		$array = $this->find($key);
		if($array) return $this->array_get($array, $item);
		return false;
	}

	/**
	* Checks if the given item exists in the array
	* that is currently available after find call
	* @return mixed
	*/
	public function isExist($key = null)
	{
		return $key = is_null($key) ? false : ($this->find($key) ? true : false);
	}
}
