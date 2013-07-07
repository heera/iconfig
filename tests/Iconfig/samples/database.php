<?php

return array(
	
	'default' => 'mysql',

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => 'public/caliber.sqlite',
			'prefix'   => 'cb_',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'caliber',
			'username'  => 'root',
			'password'  => 'bossboss',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'cb_',
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'caliber',
			'username' => 'postgres',
			'password' => 'bossboss',
			'charset'  => 'utf8',
			'prefix'   => 'cb_',
			'schema'   => 'public',
		),

	),
);