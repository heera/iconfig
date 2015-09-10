<?php namespace Iconfig;

class ConfigTest extends \PHPUnit_Framework_Testcase
{
	public function testInstanciation()
	{
		$config = new Config(__DIR__.'/samples');
		$this->assertInstanceOf('Iconfig\\Config', $config);
	}
	
	public function testSetterGetterWithClosureAndDatabaseFile()
	{
		$config = new Config(__DIR__.'/samples');
		$mysql = $config->setDatabase('connections.sqlite.driver', 'sqlites')->getDatabase('connections', function($array){
			if(is_array($array) && array_key_exists('mysql', $array)) {
				return $array['mysql'];
			}
			return $array;
		});
		
		$expected = array(
			'driver' => 'mysql',
			'host' => 'localhost',
			'database' => 'caliber',
			'username' => 'root',
			'password' => 'bossboss',
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix' => 'cb_',
			);
			
		$this->assertEquals($expected, $mysql);
	}
	
	public function testAlias()
	{
		$config = new config(__DIR__.'/samples', 'Config');
		$this->assertInstanceOf('Iconfig\\Config', $config);

		$d1 = \Config::getDatabase('default');
		$d2 = $config->getDatabase('default');
		$this->assertEquals($d1, $d2);
		$this->assertInstanceOf('Iconfig\AliasFacade', new \Config);
	}
}
