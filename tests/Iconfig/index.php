<?php

require_once "../../vendor/autoload.php";

new Iconfig\Config('samples', 'Config');

if(Config::isExist('default')) {
	echo Config::getDatabase('default');	
}

echo "<pre>";

echo "Set driver for sqlite and get connections and then filter usng closure/callback and return<br />";

$sqlite = Config::getDatabase('connections', function($data){
	if(is_array($data) && array_key_exists('sqlite', $data)) {
		Config::setDatabase('connections.sqlite.driver', 'myNewSqliteDriver');
		return Config::getDatabase('connections');
	}
});

print_r($sqlite);

echo "Print connections<br />";

$connections = Config::setDatabase('default', 'oracle')->find('connections');
print_r($connections);



echo "Print All Configurations<br />";

$all = Config::getAll();
print_r($all);
