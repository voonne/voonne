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
use Kdyby\Console\DI\ConsoleExtension;
use Kdyby\Doctrine\Mapping\AnnotationDriver;
use Kdyby\Translation\Translator;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\DI\CompilerExtension;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Voonne\Assets\AssetsManager;
use Voonne\Layouts\Layout1\ILayout1Factory;
use Voonne\Layouts\Layout21\ILayout21Factory;
use Voonne\Layouts\LayoutManager;
use Voonne\Pages\PageManager;
use Voonne\Panels\Panel;
use Voonne\Panels\Renderers\BasicPanelRenderer\BasicPanelRendererFactory;
use Voonne\Panels\Renderers\BlankPanelRenderer\BlankPanelRendererFactory;
use Voonne\Panels\Renderers\FormPanelRenderer\FormPanelRendererFactory;
use Voonne\Panels\Renderers\RendererManager;
use Voonne\Security\Authenticator;
use Voonne\Security\User;
use Voonne\Storage\Adapters\FileSystemAdapter;
use Voonne\Storage\StorageManager;
use Voonne\Voonne\AdminModule\Forms\SignInFormFactory;
use Voonne\Voonne\AssertionException;
use Voonne\Voonne\Console\InstallCommand;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Content\Latte\Engine;
use Voonne\Voonne\Controls\DomainSelect\IDomainSelectControlFactory;
use Voonne\Voonne\Controls\FlashMessage\IFlashMessageControlFactory;
use Voonne\Voonne\Controls\FormError\IFormErrorControlFactory;
use Voonne\Voonne\Controls\Menu\IMenuControlFactory;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\DomainLanguageRepository;
use Voonne\Voonne\Model\Repositories\DomainRepository;
use Voonne\Voonne\Model\Repositories\LanguageRepository;
use Voonne\Voonne\Model\Repositories\UserRepository;
use Voonne\Voonne\Routers\RouterFactory;


class VoonneExtension extends CompilerExtension
{


	public $defaults = [
		'storageProvider' => null
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		/* facades */

		$builder->addDefinition($this->name . '.userFacade')
			->setClass(UserFacade::class);

		/* repositories */

		$builder->addDefinition($this->name . '.domainLanguageRepository')
			->setClass(DomainLanguageRepository::class);

		$builder->addDefinition($this->name . '.domainRepository')
			->setClass(DomainRepository::class);

		$builder->addDefinition($this->name . '.languageRepository')
			->setClass(LanguageRepository::class);

		$builder->addDefinition($this->name . '.userRepository')
			->setClass(UserRepository::class);

		/* forms */

		$builder->addDefinition($this->name . '.signInFormFactory')
			->setClass(SignInFormFactory::class);

		/* controls */

		$builder->addDefinition($this->name . '.domainSelectControlFactory')
			->setImplement(IDomainSelectControlFactory::class);

		$builder->addDefinition($this->name . '.flashMessageControlFactory')
			->setImplement(IFlashMessageControlFactory::class);

		$builder->addDefinition($this->name . '.formErrorControlFactory')
			->setImplement(IFormErrorControlFactory::class);

		$builder->addDefinition($this->name . '.menuControlFactory')
			->setImplement(IMenuControlFactory::class);

		/* content */

		$builder->addDefinition($this->name . '.contentForm')
			->setClass(ContentForm::class);

		$builder->addDefinition($this->name . '.templateFactory')
			->setClass(TemplateFactory::class, ['@' . $this->name . '.latteFactory'])
			->setAutowired(false);

		/* pages */

		$builder->addDefinition($this->name . '.pageManager')
			->setClass(PageManager::class);

		/* layouts */

		$builder->addDefinition($this->name . '.layoutManager')
			->setClass(LayoutManager::class);

		$builder->addDefinition($this->name . '.layout1Factory')
			->setImplement(ILayout1Factory::class)
			->addTag(LayoutManager::TAG_LAYOUT);

		$builder->addDefinition($this->name . '.layout21Factory')
			->setImplement(ILayout21Factory::class)
			->addTag(LayoutManager::TAG_LAYOUT);

		/* panels */

		$builder->addDefinition($this->name . '.rendererManager')
			->setClass(RendererManager::class);

		$builder->addDefinition($this->name . '.basicPanelRendererFactory')
			->setClass(BasicPanelRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->name . '.blankPanelRendererFactory')
			->setClass(BlankPanelRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->name . '.formPanelRendererFactory')
			->setClass(FormPanelRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		/* router */

		$builder->addDefinition($this->name . '.router')
			->setFactory(RouterFactory::class . '::createRouter')
			->setAutowired(false);

		/* commands */

		$builder->addDefinition($this->name . '.cli.install')
			->setClass(InstallCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		/* storage */

		if (empty($config['storageProvider'])) {
			throw new AssertionException("Please configure 'storageProvider' for the Voonne extensions using the section '{$this->name}:' in your config file.");
		}

		$builder->addDefinition($this->name . '.storageManager')
			->setClass(StorageManager::class, [$config['storageProvider']]);

		/* authentication and authorization */

		$builder->addDefinition($this->name . '.authenticator')
			->setClass(Authenticator::class);

		$builder->addDefinition($this->name . '.user')
			->setClass(User::class);

		/* assets */

		$builder->addDefinition($this->name . '.assetsManager')
			->setClass(AssetsManager::class)
			->addSetup('addScript', ['admin', __DIR__ . '/../../dist/scripts/admin.js'])
			->addSetup('addScript', ['sign-in', __DIR__ . '/../../dist/scripts/sign-in.js'])
			->addSetup('addStyle', ['admin', __DIR__ . '/../../dist/styles/admin.css'])
			->addSetup('addStyle', ['sign-in', __DIR__ . '/../../dist/styles/sign-in.css'])
			->addSetup('addAsset', ['styles/admin.css.map', __DIR__ . '/../../dist/styles/admin.css.map'])
			->addSetup('addAsset', ['styles/sign-in.css.map', __DIR__ . '/../../dist/styles/sign-in.css.map'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.svg', __DIR__ . '/../../dist/fonts/fontawesome-webfont.svg'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.ttf', __DIR__ . '/../../dist/fonts/fontawesome-webfont.ttf'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.woff', __DIR__ . '/../../dist/fonts/fontawesome-webfont.woff'])
			->addSetup('addAsset', ['fonts/fontawesome-webfont.woff2', __DIR__ . '/../../dist/fonts/fontawesome-webfont.woff2'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.svg', __DIR__ . '/../../dist/fonts/glyphicons-halflings-regular.svg'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.ttf', __DIR__ . '/../../dist/fonts/glyphicons-halflings-regular.ttf'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.woff', __DIR__ . '/../../dist/fonts/glyphicons-halflings-regular.woff'])
			->addSetup('addAsset', ['fonts/glyphicons-halflings-regular.woff2', __DIR__ . '/../../dist/fonts/glyphicons-halflings-regular.woff2']);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		/* router and mapping */

		$builder->getDefinition($builder->getByType(IPresenterFactory::class))
			->addSetup('setMapping', [['Admin' => 'Voonne\Voonne\AdminModule\*Module\Presenters\*Presenter']]);

		// inspired by: https://github.com/kdyby/console/blob/master/src/Kdyby/Console/DI/ConsoleExtension.php
		$routerServiceName = $builder->getByType(IRouter::class) ? : 'router';
		$builder->addDefinition($this->name . '.originalRouter', $builder->getDefinition($routerServiceName))
			->setAutowired(false);

		$builder->removeDefinition($routerServiceName);

		$builder->addDefinition($routerServiceName)
			->setClass(RouteList::class)
			->addSetup('offsetSet', [NULL, '@' . $this->name . '.router'])
			->addSetup('offsetSet', [NULL, '@' . $this->name . '.originalRouter']);

		/* translator */

		$translatorName = $builder->getByType(Translator::class);

		if(empty($translatorName)) {
			throw new InvalidStateException('Kdyby/Translation not found. Please register Kdyby/Translation as an extension.');
		}

		// inspired by: https://github.com/Kdyby/Translation/blob/master/src/Kdyby/Translation/DI/TranslationExtension.php
		foreach (Finder::findFiles('*.*.neon')->from(__DIR__ . '/../translations') as $file) {
			if (!$m = Strings::match($file->getFilename(), '~^(?P<domain>.*?)\.(?P<locale>[^\.]+)\.(?P<format>[^\.]+)$~')) {
				continue;
			}

			$builder->getDefinition($translatorName)
				->addSetup('addResource', [$m['format'], realpath($file->getPathname()), $m['locale'], 'voonne-' . $m['domain']]);
		}

		/* doctrine */

		$metadataDriver = $this->getMetadataDriver();

		if (empty($metadataDriver)) {
			throw new InvalidStateException('Kdyby/Doctrine not found. Please register Kdyby/Doctrine as an extension.');
		}

		// inspired by: https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/DI/OrmExtension.php
		$this->getContainerBuilder()->addDefinition($this->name . '.doctrine.annotations')
			->setClass('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')
			->setFactory(AnnotationDriver::class, [
				0 => [0 => __DIR__ . '/..'],
				2 => '@doctrine.cache.default.metadata'
			])
			->setAutowired(false);

		$metadataDriver->addSetup('addDriver', ['@' . $this->name . '.doctrine.annotations', 'Voonne\Voonne']);

		/* content */

		$latteFactoryDefinition = $builder->addDefinition('' . $this->name . '.latteFactory')
			->setClass(Engine::class, ['@' . $this->name . '.contentForm'])
			->setImplement(ILatteFactory::class)
			->setAutowired(false);

		foreach ($builder->getDefinition('latte.latteFactory')->getSetup() as $setup) {
			$latteFactoryDefinition->addSetup($setup);
		}

		foreach ($builder->getDefinitions() as $definition) {
			if(is_subclass_of($definition->getClass(), Panel::class)) {
				$definition->addSetup('setTemplateFactory', ['@' . $this->name . '.templateFactory']);
			}
		}
	}


	private function getMetadataDriver()
	{
		$builder = $this->getContainerBuilder();

		foreach ($builder->getDefinitions() as $definition) {
			if ($definition->getClass() == MappingDriverChain::class) {
				return $definition;
			}
		}

		return null;
	}

}
