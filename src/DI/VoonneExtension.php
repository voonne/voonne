<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\DI;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Kdyby\Console\DI\ConsoleExtension;
use Kdyby\Doctrine\DI\OrmExtension;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
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
use Voonne\Domains\DomainManager;
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
use Voonne\Security\Authorizator;
use Voonne\Security\PermissionManager;
use Voonne\Storage\Latte\Macros;
use Voonne\Storage\StorageManager;
use Voonne\Voonne\AdminModule\Forms\LostPasswordFormFactory;
use Voonne\Voonne\AdminModule\Forms\NewPasswordFormFactory;
use Voonne\Voonne\AdminModule\Forms\SignInFormFactory;
use Voonne\Voonne\AssertionException;
use Voonne\Voonne\Console\GenerateModuleCommand;
use Voonne\Voonne\Console\Helpers\StateHelper;
use Voonne\Voonne\Console\InstallCommand;
use Voonne\Voonne\Console\PermissionListCommand;
use Voonne\Voonne\Console\RoleAddPrivilegeCommand;
use Voonne\Voonne\Console\RoleCreateCommand;
use Voonne\Voonne\Console\RoleListCommand;
use Voonne\Voonne\Console\RoleRemoveCommand;
use Voonne\Voonne\Console\RoleRemovePrivilegeCommand;
use Voonne\Voonne\Console\UserAddRoleCommand;
use Voonne\Voonne\Console\UserCreateCommand;
use Voonne\Voonne\Console\UserListCommand;
use Voonne\Voonne\Console\UserRemoveCommand;
use Voonne\Voonne\Console\UserRemoveRoleCommand;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Content\Latte\Engine;
use Voonne\Voonne\Controls\DomainSelect\IDomainSelectControlFactory;
use Voonne\Voonne\Controls\FlashMessage\IFlashMessageControlFactory;
use Voonne\Voonne\Controls\FormError\IFormErrorControlFactory;
use Voonne\Voonne\Controls\Menu\IMenuControlFactory;
use Voonne\Voonne\InvalidStateException;
use Voonne\Voonne\Listeners\EmailListener;
use Voonne\Voonne\Model\Entities\Domain;
use Voonne\Voonne\Model\Entities\DomainLanguage;
use Voonne\Voonne\Model\Entities\Language;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Resource;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Entities\Zone;
use Voonne\Voonne\Model\Facades\LostPasswordFacade;
use Voonne\Voonne\Model\Facades\RoleFacade;
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\ZoneRepository;
use Voonne\Voonne\Model\Repositories\DomainLanguageRepository;
use Voonne\Voonne\Model\Repositories\DomainRepository;
use Voonne\Voonne\Model\Repositories\LanguageRepository;
use Voonne\Voonne\Model\Repositories\LostPasswordRepository;
use Voonne\Voonne\Model\Repositories\PrivilegeRepository;
use Voonne\Voonne\Model\Repositories\ResourceRepository;
use Voonne\Voonne\Model\Repositories\RoleRepository;
use Voonne\Voonne\Model\Repositories\UserRepository;
use Voonne\Voonne\Routers\RouterFactory;
use Voonne\Widgets\WidgetManager;


class VoonneExtension extends CompilerExtension
{

	/**
	 * @var array
	 */
	public $defaults = [
		'storageAdapter' => null
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		/* facades */

		$builder->addDefinition($this->prefix('lostPasswordFacade'))
			->setType(LostPasswordFacade::class);

		$builder->addDefinition($this->prefix('roleFacade'))
			->setType(RoleFacade::class);

		$builder->addDefinition($this->prefix('userFacade'))
			->setType(UserFacade::class);

		/* repositories */

		$builder->addDefinition($this->prefix('domainLanguageRepository'))
			->setType(DomainLanguageRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => DomainLanguage::class]);

		$builder->addDefinition($this->prefix('domainRepository'))
			->setType(DomainRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => Domain::class]);

		$builder->addDefinition($this->prefix('languageRepository'))
			->setType(LanguageRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => Language::class]);

		$builder->addDefinition($this->prefix('lostPasswordRepository'))
			->setType(LostPasswordRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => LostPassword::class]);

		$builder->addDefinition($this->prefix('PrivilegeRepository'))
			->setType(PrivilegeRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => Privilege::class]);

		$builder->addDefinition($this->prefix('resourceRepository'))
			->setType(ResourceRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => Resource::class]);

		$builder->addDefinition($this->prefix('roleRepository'))
			->setType(RoleRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => Role::class]);

		$builder->addDefinition($this->prefix('userRepository'))
			->setType(UserRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => User::class]);

		$builder->addDefinition($this->prefix('zoneRepository'))
			->setType(ZoneRepository::class)
			->setTags([OrmExtension::TAG_REPOSITORY_ENTITY => Zone::class]);

		/* forms */

		$builder->addDefinition($this->prefix('lostPasswordFormFactory'))
			->setType(LostPasswordFormFactory::class);

		$builder->addDefinition($this->prefix('newPasswordFormFactory'))
			->setType(NewPasswordFormFactory::class);

		$builder->addDefinition($this->prefix('signInFormFactory'))
			->setType(SignInFormFactory::class);

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
			->setType(EmailListener::class)
			->addTag(EventsExtension::TAG_SUBSCRIBER);

		/* content */

		$builder->addDefinition($this->prefix('contentForm'))
			->setType(ContentForm::class);

		$builder->addDefinition($this->prefix('templateFactory'))
			->setFactory(TemplateFactory::class, ['@' . $this->prefix('latteFactory')])
			->setAutowired(false);

		/* pages */

		$builder->addDefinition($this->prefix('pageManager'))
			->setType(PageManager::class);

		/* layouts */

		$builder->addDefinition($this->prefix('layoutManager'))
			->setType(LayoutManager::class);

		$builder->addDefinition($this->prefix('layout1Factory'))
			->setImplement(ILayout1Factory::class)
			->addTag(LayoutManager::TAG_LAYOUT);

		$builder->addDefinition($this->prefix('layout21Factory'))
			->setImplement(ILayout21Factory::class)
			->addTag(LayoutManager::TAG_LAYOUT);

		/* panels */

		$builder->addDefinition($this->prefix('rendererManager'))
			->setType(RendererManager::class);

		$builder->addDefinition($this->prefix('basicRendererFactory'))
			->setType(BasicRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->prefix('blankRendererFactory'))
			->setType(BlankRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->prefix('formRendererFactory'))
			->setType(FormRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		$builder->addDefinition($this->prefix('tableRendererFactory'))
			->setType(TableRendererFactory::class)
			->addTag(RendererManager::TAG_RENDERER);

		/* widgets */

		$builder->addDefinition($this->prefix('widgetManager'))
			->setType(WidgetManager::class);

		/* router */

		$builder->addDefinition($this->prefix('router'))
			->setFactory(RouterFactory::class . '::createRouter')
			->setAutowired(false);

		/* commands */

		$builder->addDefinition($this->prefix('commands.helpers.stateHelper'))
			->setType(StateHelper::class)
			->addTag(ConsoleExtension::TAG_HELPER);

		$builder->addDefinition($this->prefix('commands.generate.module'))
			->setType(GenerateModuleCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.install'))
			->setType(InstallCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.permission.list'))
			->setType(PermissionListCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.role.addPrivilege'))
			->setType(RoleAddPrivilegeCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.role.create'))
			->setType(RoleCreateCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.role.list'))
			->setType(RoleListCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.role.remove'))
			->setType(RoleRemoveCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.role.removePrivilege'))
			->setType(RoleRemovePrivilegeCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.user.addRole'))
			->setType(UserAddRoleCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.user.create'))
			->setType(UserCreateCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.user.list'))
			->setType(UserListCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.user.remove'))
			->setType(UserRemoveCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		$builder->addDefinition($this->prefix('commands.user.removeRole'))
			->setType(UserRemoveRoleCommand::class)
			->addTag(ConsoleExtension::TAG_COMMAND);

		/* domains */

		$builder->addDefinition($this->prefix('domainManager'))
			->setType(DomainManager::class);

		/* storage */

		if (empty($config['storageAdapter'])) {
			throw new AssertionException("Please configure 'storageAdapter' for the Voonne extensions using the section '{$this->name}:' in your config file.");
		}

		$builder->addDefinition($this->prefix('storageAdapter'))
			->setFactory(StorageManager::class, [$config['storageAdapter']]);

		$builder->getDefinition('latte.latteFactory')
			->addSetup('?->onCompile[] = function($engine) { ' . Macros::class . '::install($engine->getCompiler()); }', ['@self']);

		/* security */

		$builder->addDefinition($this->prefix('authenticator'))
			->setType(Authenticator::class);

		$builder->addDefinition($this->prefix('authorizator'))
			->setType(Authorizator::class);

		$builder->addDefinition($this->prefix('permissionManager'))
			->setType(PermissionManager::class)
			->addSetup('addZone', ['admin', 'Administration']);

		$builder->addDefinition($this->prefix('user'))
			->setType(\Voonne\Security\User::class);

		/* assets */

		$builder->addDefinition($this->prefix('assetsManager'))
			->setType(AssetsManager::class)
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
			->setType(RouteList::class)
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
		$builder->addDefinition($this->prefix('doctrine.annotations'))
			->setType(MappingDriver::class)
			->setFactory(AnnotationDriver::class, [
				0 => '@Doctrine\Common\Annotations\Reader',
				1 => [__DIR__ . '/..']
			])
			->setAutowired(false);

		$metadataDriver->addSetup('addDriver', [
			'@' . $this->prefix('doctrine.annotations'),
			'Voonne\Voonne'
		]);

		/* content */

		$latteFactoryDefinition = $builder->addDefinition($this->prefix('latteFactory'))
			->setFactory(Engine::class, ['@' . $this->prefix('contentForm')])
			->setImplement(ILatteFactory::class)
			->setAutowired(false);

		foreach ($builder->getDefinition('latte.latteFactory')->getSetup() as $setup) {
			$latteFactoryDefinition->addSetup($setup);
		}

		foreach ($builder->getDefinitions() as $definition) {
			if(is_subclass_of($definition->getType(), Panel::class)) {
				$definition->addSetup('setTemplateFactory', ['@' . $this->prefix('templateFactory')]);
			}
		}
	}


	private function getMetadataDriver()
	{
		$builder = $this->getContainerBuilder();

		foreach ($builder->getDefinitions() as $definition) {
			if ($definition->getType() == MappingDriverChain::class) {
				return $definition;
			}
		}

		return null;
	}

}
