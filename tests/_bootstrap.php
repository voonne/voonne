<?php
// This is global bootstrap for autoloading

(new \Nette\Loaders\RobotLoader())
	->addDirectory(__DIR__ . '/../src')
	->register();

if(!ini_get('date.timezone')) {
	date_default_timezone_set('GMT');
}