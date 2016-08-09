<?php
// This is global bootstrap for autoloading

(new \Nette\Loaders\RobotLoader())
	->addDirectory(__DIR__ . '/../src')
	->register();
