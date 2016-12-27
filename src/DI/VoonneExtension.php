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
use Kdyby\Events\DI\EventsExtension;
use Kdyby\Translation\Translator;
use Nette\Application\IPresenterFactory;
use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\DI\CompilerExtension;
use Nette\Utils\Finder;
use Nette\Utils\Strings;
use Voonne\Assets\AssetsManager;
use Voonne\Layouts\Layout1\ILayout1Factory;
use Voonne\Layouts\Layout21\ILayout21Factory;
use Voonne\Layouts\LayoutManager;
use Voonne\Pages\PageManager;
use Voonne\Panels\Panels\Panel;
use Voonne\Panels\Renderers\BasicRenderer\BasicRendererFactory;
use Voonne\Panels\Renderers\BlankRenderer\BlankRendererFactory;
use Voonne\Panels\Renderers\FormRenderer\FormRendererFactory;
use Voonne\Panels\Renderers\RendererManager;
use Voonne\Panels\Renderers\TableRenderer\TableRendererFactory;
use Voonne\Security\Authenticator;
use Voonne\Security\User;
use Voonne\Storage\StorageManager;
use Voonne\Voonne\AdminModule\Forms\LostPasswordFormFactory;
use Voonne\Voonne\AdminModule\Forms\NewPasswordFormFactory;
use Voonne\Voonne\AdminModule\Forms\SignInFormFactory;
use Voonne\Voonne\AssertionException;
use Voonne\Voonne\Console\DomainCreateCommand;
use Voonne\Voonne\Console\Helpers\StateHelper;
use Voonne\Voonne\Console\InstallCommand;
use Voonne\Voonne\Console\UserCreateCommand;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Content\Latte\Engine;
use Voonne\Voonne\Controls\DomainSelect\IDomainSelectControlFactory;
use Voonne\Voonne\Controls\FlashMessage\IFlashMessageControlFactory;
use Voonne\Voonne\Controls\FormError\IFormErrorControlFactory;
use Voonne\Voonne\Controls\Menu\IMenuControlFactory;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Listeners\EmailListener;
use Voonne\Voonne\Model\Facades\LostPasswordFacade;
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\DomainLanguageRepository;
use Voonne\Voonne\Model\Repositories\DomainRepository;
use Voonne\Voonne\Model\Repositories\LanguageRepository;
use Voonne\Voonne\Model\Repositories\LostPasswordRepository;
use Voonne\Voonne\Model\Repositories\UserRepository;
use Voonne\Voonne\Routers\RouterFactory;


class VoonneExtension extends CompilerExtension
{

	/**
	 * @var array
	 */
	public $defaults = [
		'storageProvider' => null
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		/* facades */

		$builder->addDefinition($this->prefix('lostPasswordFacade'))
			->setClass(LostPasswordFacade::class);

		$builder->addDefinition($this->prefix('userFacade'))
			->setClass(UserFacade::class);

		/* repositories */

		$builder->addDefinition($this->prefix('domainLanguageRepository'))
			->setClass(DomainLanguageRepository::class);

		$builder->addDefinition($this->prefix('domainRepository'))
			->setClass(DomainRepository::class);

		$builder->addDefinition($this->prefix('languageRepository'))
			->setClass(LanguageRepository::class);

		$builder->addDefinition($this->prefix('lostPasswordRepository'))
			->setClass(LostPasswordRepository::class);

		$builder->addDefinition($this->prefix('userRepository'))
			->setClass(UserRepository::class);

		/* forms */

		$builder->addDefinition($this->prefix('lostPasswordFormFactory'))
			->setClass(LostPasswordFormFactory::class);

		$builder->addDefinition($this->prefix('newPasswordFormFactory'))
			->setClass(NewPasswordFormFactory::class);

		$builder->addDefinition($this->prefix('signInFormFactory'))
			->setClass(SignInFormFactory::class);

		/* controls */

		$builder->addDefinition($this->prefix('domainSelectControlFactory'))
			->setImplement(IDomainSelectControlFactory::class);

		$builder->addDefinition($this->prefix('flashMessageControlFactory'))
			->setImplement(IFlashMessageControlFactory::class);

		$builder->addDefinition($this->prefix('formErrorControlFactory'))
			->setImplement(IFormErrorControlFactory::class);

		$builder->addDefinition($this->prefix('menuControlFactory'))
			->setImplement(IMenuControlFactory::class);

		/* listeners */

		$builder->addDefinition($this->prefix('emailListener'))
			->setClass(EmailListener::class)
			->addTag(EventsExtension::TAG_SUBSCRIBER);

		/* content */

		$builder->addDefinition($this->prefix('contentForm'))
			->setClass(ContentForm::class);

		$builder->addDefinition($this->prefix('templateFactory'))
			->setClass(TemplateFactory::class, ['@' . $this->prefix('latteFactory')])
			->setAutowired(false);

		/* pages */

		$builder->addDefinition($this->prefix('pageManager'))
			->setClass(PageManager::class);

		/* layouts */

		$builder->addDefinition($this->prefix('layoutManager'))
			->setClass(LayoutManager::class);

		$builder->addDefinition($this->prefix('layout1Factory'))
			->setImplement(ILayout1Factory::class)
			->addTag(LayoutManager::TAG_LAYOUT);

		$builder->addDefinition($this->prefix('layout21Factory'))
			->setImplement(ILayout21Factory::class)
			->addTag(LayoutManager::TAG_LAYOUT);

		/* panels */

		$builder->addDefinition($this->prefix('rendererManager'))
			->setClass(RendererManager::class);

		$builder->addDefinition($this->prefix('basicRendererFactory'))
			->setClass(BasicRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->prefix('blankRendererFactory'))
			->setClass(BlankRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->prefix('formRendererFactory'))
			->setClass(FormRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->prefix('tableRendererFactory'))
			->setClass(TableRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		/* router */

		$builder->addDefinition($this->prefix('router'))
			->setFactory(RouterFactory::class . '::createRouter')
			->setAutowired(false);

		/* commands */

		$builder->addDefinition($this->prefix('cli.stateHelper'))
			->setClass(StateHelper::class)
			->addTag(ConsoleExtension::TAG_HELPER);

		$builder->addDefinition($this->prefix('cli.user.create'))
			->setClass(UserCreateCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('cli.install'))
			->setClass(InstallCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('cli.domain.create'))
			->setClass(DomainCreateCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		/* storage */

		if (empty($config['storageAdapter'])) {
			throw new AssertionException("Please configure 'storageAdapter' for the Voonne extensions using the section '{$this->name}:' in your config file.");
		}

		$builder->addDefinition($this->prefix('storageAdapter'))
			->setClass(StorageManager::class, [$config['storageAdapter']]);

		/* authentication and authorization */

		$builder->addDefinition($this->prefix('authenticator'))
			->setClass(Authenticator::class);

		$builder->addDefinition($this->prefix('user'))
			->setClass(User::class);

		/* assets */

		$builder->addDefinition($this->prefix('assetsManager'))
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
		$builder->addDefinition($this->prefix('originalRouter'), $builder->getDefinition($routerServiceName))
			->setAutowired(false);

		$builder->removeDefinition($routerServiceName);

		$builder->addDefinition($routerServiceName)
			->setClass(RouteList::class)
			->addSetup('offsetSet', [NULL, '@' . $this->prefix('router')])
			->addSetup('offsetSet', [NULL, '@' . $this->prefix('originalRouter')]);

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

		$metadataDriver->addSetup('addDriver', ['@' . $this->prefix('doctrine.annotations'), 'Voonne\Voonne']);

		/* content */

		$latteFactoryDefinition = $builder->addDefinition('' . $this->prefix('latteFactory'))
			->setClass(Engine::class, ['@' . $this->prefix('contentForm')])
			->setImplement(ILatteFactory::class)
			->setAutowired(false);

		foreach ($builder->getDefinition('latte.latteFactory')->getSetup() as $setup) {
			$latteFactoryDefinition->addSetup($setup);
		}

		foreach ($builder->getDefinitions() as $definition) {
			if(is_subclass_of($definition->getClass(), Panel::class)) {
				$definition->addSetup('setTemplateFactory', ['@' . $this->prefix('templateFactory')]);
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
