<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\DI;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Kdyby\Doctrine\DI\OrmExtension;
use Kdyby\Doctrine\Mapping\AnnotationDriver;
use Kdyby\Translation\Translator;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Voonne\Voonne\Assets\AssetsManager;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\UserRepository;
use Voonne\Voonne\Routers\RouterFactory;


class VoonneExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		/* facades */

		$builder->addDefinition('voonne.userFacade')
			->setClass(UserFacade::class);

		/* repositories */

		$builder->addDefinition('voonne.userRepository')
			->setClass(UserRepository::class);

		/* router */

		$builder->addDefinition('voonne.router')
			->setFactory(RouterFactory::class . '::createRouter')
			->setAutowired(false);

		/* assets */

		$builder->addDefinition('voonne.assetsManager')
			->setClass(AssetsManager::class)
			->addSetup('addScript', ['admin', __DIR__ . '/../../../../dist/scripts/admin.js'])
			->addSetup('addScript', ['sign-in', __DIR__ . '/../../../../dist/scripts/sign-in.js'])
			->addSetup('addStyle', ['admin', __DIR__ . '/../../../../dist/styles/admin.css'])
			->addSetup('addStyle', ['sign-in', __DIR__ . '/../../../../dist/styles/sign-in.css'])
			->addSetup('addAsset', ['styles/admin.css.map', __DIR__ . '/../../../../dist/styles/admin.css.map'])
			->addSetup('addAsset', ['styles/sign-in.css.map', __DIR__ . '/../../../../dist/styles/sign-in.css.map'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.svg', __DIR__ . '/../../../../dist/fonts/fontawesome-webfont.svg'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.ttf', __DIR__ . '/../../../../dist/fonts/fontawesome-webfont.ttf'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.woff', __DIR__ . '/../../../../dist/fonts/fontawesome-webfont.woff'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.woff2', __DIR__ . '/../../../../dist/fonts/fontawesome-webfont.woff2'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.svg', __DIR__ . '/../../../../dist/fonts/glyphicons-halflings-regular.svg'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.ttf', __DIR__ . '/../../../../dist/fonts/glyphicons-halflings-regular.ttf'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.woff', __DIR__ . '/../../../../dist/fonts/glyphicons-halflings-regular.woff'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.woff2', __DIR__ . '/../../../../dist/fonts/glyphicons-halflings-regular.woff2']);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		/* router and mapping */

		$builder->getDefinition($builder->getByType(IPresenterFactory::class))
			->addSetup('setMapping', [['Admin' => 'Voonne\Voonne\AdminModule\*Module\Presenters\*Presenter']]);

		// inspired by: https://github.com/kdyby/console/blob/master/src/Kdyby/Console/DI/ConsoleExtension.php
		$routerServiceName = $builder->getByType(IRouter::class) ? : 'router';
		$builder->addDefinition('voonne.originalRouter', $builder->getDefinition($routerServiceName))
			->setAutowired(false);

		$builder->removeDefinition($routerServiceName);

		$builder->addDefinition($routerServiceName)
			->setClass(RouteList::class)
			->addSetup('offsetSet', [NULL, '@voonne.router'])
			->addSetup('offsetSet', [NULL, '@voonne.originalRouter']);

		/* translator */

		$translatorName = $builder->getByType(Translator::class);

		if(empty($translatorName)) {
			throw new InvalidStateException('Kdyby/Translation not found. Please register Kdyby/Translation as an extension.');
		}

		// inspired by: https://github.com/Kdyby/Translation/blob/master/src/Kdyby/Translation/DI/TranslationExtension.php
		foreach(Finder::findFiles('*.*.neon')->from(__DIR__ . '/../translations') as $file) {
			if (!$m = Strings::match($file->getFilename(), '~^(?P<domain>.*?)\.(?P<locale>[^\.]+)\.(?P<format>[^\.]+)$~')) {
				continue;
			}

			$builder->getDefinition($translatorName)
				->addSetup('addResource', [$m['format'], realpath($file->getPathname()), $m['locale'], 'voonne-' . $m['domain']]);
		}

		/* doctrine */

		$metadataDriver = $this->getMetadataDriver();

		if(empty($metadataDriver)) {
			throw new InvalidStateException('Kdyby/Doctrine not found. Please register Kdyby/Doctrine as an extension.');
		}

		// inspired by: https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/DI/OrmExtension.php
		$this->getContainerBuilder()->addDefinition('voonne.doctrine.annotations')
			->setClass('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')
			->setFactory(AnnotationDriver::class, [
				0 => [0 => __DIR__ . '/..'],
				2 => '@doctrine.cache.default.metadata'
			])
			->setAutowired(FALSE)
			->setInject(FALSE);

		$metadataDriver->addSetup('addDriver', ['@voonne.doctrine.annotations', 'Voonne\Voonne']);
	}


	private function getMetadataDriver()
	{
		$builder = $this->getContainerBuilder();

		foreach($builder->getDefinitions() as $definition) {
			if($definition->getClass() == MappingDriverChain::class) {
				return $definition;
			}
		}

		return null;
	}

}
