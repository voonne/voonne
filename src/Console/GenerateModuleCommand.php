<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use Nette\DI\Container;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Voonne\IOException;


class GenerateModuleCommand extends Command
{

	/**
	 * @var Container
	 * @inject
	 */
	public $container;

	/**
	 * @var string
	 */
	private $name = 'voonne:generate:module';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Generates module boilerplate.');

		$this->setDefinition([
			new InputArgument('name', InputArgument::REQUIRED),
			new InputArgument('namespace', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$name = $input->getArgument('name');
			$namespace = $input->getArgument('namespace');

			$this->createModulesDirectory();
			$this->createModuleDirectoryStructure($name);
			$this->createModuleFiles($name, $namespace);

			$output->writeln(sprintf('Module with name "%s" has been successfully generated.', $name));

			return 0;
		} catch (IOException $e) {
			$output->writeln(sprintf('<fg=red>%s</>', $e->getMessage()));

			return 1;
		}
	}


	private function createModulesDirectory()
	{
		$path = sprintf('%s/Modules', $this->container->getParameters()['appDir']);

		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
	}


	private function createModuleDirectoryStructure($name)
	{
		$path = sprintf('%s/Modules/%s', $this->container->getParameters()['appDir'], $name);

		if (!is_dir($path)) {
			mkdir(sprintf('%s/DI', $path), 0777, true);
			mkdir(sprintf('%s/Model/Entities', $path), 0777, true);
			mkdir(sprintf('%s/Model/Facades', $path), 0777, true);
			mkdir(sprintf('%s/Model/Repositories', $path), 0777, true);
			mkdir(sprintf('%s/Pages', $path), 0777, true);
			mkdir(sprintf('%s/Panels', $path), 0777, true);
		} else {
			throw new IOException(sprintf('Module with name "%s" already exists. You can\'t generate module twice.', $name));
		}
	}


	private function createModuleFiles($name, $namespace)
	{
		$path = sprintf('%s/Modules/%s', $this->container->getParameters()['appDir'], $name);

		// Module/DI

		file_put_contents(
			sprintf('%s/DI/%sExtension.php', $path, $name),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\DI;

use ' . $namespace . '\Modules\\' . $name . '\Model\Entities\\' . $name . ';
use ' . $namespace . '\Modules\\' . $name . '\Model\Facades\\' . $name . 'Facade;
use ' . $namespace . '\Modules\\' . $name . '\Model\Repositories\\' . $name . 'Repository;
use ' . $namespace . '\Modules\\' . $name . '\Pages\CreatePage;
use ' . $namespace . '\Modules\\' . $name . '\Pages\DefaultPage;
use ' . $namespace . '\Modules\\' . $name . '\Pages\UpdatePage;
use ' . $namespace . '\Modules\\' . $name . '\Panels\CreateFormPanel;
use ' . $namespace . '\Modules\\' . $name . '\Panels\\' . $name . 'TablePanel;
use ' . $namespace . '\Modules\\' . $name . '\Panels\UpdateFormPanel;
use Kdyby\Doctrine\DI\OrmExtension;
use Nette\DI\CompilerExtension;
use Voonne\Layouts\Layout;


class ' . $name . 'Extension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition(\'voonne.permissionManager\')
			->addSetup(\'addResource\', [\'admin\', \'' . Strings::firstLower($name) . '\', \'Module ' . $this->getLabel($name) . '\'])
			->addSetup(\'addPrivilege\', [\'admin\', \'' . Strings::firstLower($name) . '\', \'create\', \'Create\'])
			->addSetup(\'addPrivilege\', [\'admin\', \'' . Strings::firstLower($name) . '\', \'view\', \'View\'])
			->addSetup(\'addPrivilege\', [\'admin\', \'' . Strings::firstLower($name) . '\', \'update\', \'Update\'])
			->addSetup(\'addPrivilege\', [\'admin\', \'' . Strings::firstLower($name) . '\', \'remove\', \'Remove\']);

		$builder->getDefinition(\'voonne.pageManager\')
			->addSetup(\'addGroup\', [\'' . Strings::firstLower($name) . '\', \'' . $this->getLabel($name) . '\'])
			->addSetup(\'addPage\', [\'' . Strings::firstLower($name) . '\', \'@\' . $this->prefix(\'defaultPage\')])
			->addSetup(\'addPage\', [\'' . Strings::firstLower($name) . '\', \'@\' . $this->prefix(\'createPage\')])
			->addSetup(\'addPage\', [\'' . Strings::firstLower($name) . '\', \'@\' . $this->prefix(\'updatePage\')]);

		$builder->addDefinition($this->prefix(\'createPage\'))
			->setType(CreatePage::class)
			->addSetup(\'addPanel\', [\'@\' . $this->prefix(\'createFormPanel\'), [Layout::POSITION_CENTER]]);

		$builder->addDefinition($this->prefix(\'defaultPage\'))
			->setType(DefaultPage::class)
			->addSetup(\'addPanel\', [\'@\' . $this->prefix(\'' . $name . 'TablePanel\'), [Layout::POSITION_CENTER]]);

		$builder->addDefinition($this->prefix(\'updatePage\'))
			->setType(UpdatePage::class)
			->addSetup(\'addPanel\', [\'@\' . $this->prefix(\'updateFormPanel\'), [Layout::POSITION_CENTER]]);

		$builder->addDefinition($this->prefix(\'createFormPanel\'))
			->setType(CreateFormPanel::class);

		$builder->addDefinition($this->prefix(\'' . $name . 'TablePanel\'))
			->setType(' . $name . 'TablePanel::class);

		$builder->addDefinition($this->prefix(\'updateFormPanel\'))
			->setType(UpdateFormPanel::class);

		$builder->addDefinition($this->prefix(\'' . $name . 'Facade\'))
			->setType(' . $name . 'Facade::class);

		$builder->addDefinition($this->prefix(\'' . $name . 'Repository\'))
			->setType(' . $name . 'Repository::class)
			->addTag(OrmExtension::TAG_REPOSITORY_ENTITY, ' . $name . '::class);
	}

}
'
		);

		// Module/Model/Entities

		file_put_contents(
			sprintf('%s/Model/Entities/%s.php', $path, $name),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Model\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\UniversallyUniqueIdentifier;
use Nette\SmartObject;


/**
 * @ORM\Entity(repositoryClass="' . $namespace . '\Modules\\' . $name . '\Model\Repositories\\' . $name . 'Repository")
 */
class ' . $name . '
{

	use SmartObject;
	use UniversallyUniqueIdentifier;

}
'
		);

		// Module/Model/Facades

		file_put_contents(
			sprintf('%s/Model/Facades/%sFacade.php', $path, $name),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Model\Facades;

use ' . $namespace . '\Modules\\' . $name . '\Model\Entities\\' . $name . ';
use Doctrine\ORM\EntityManagerInterface;
use Nette\SmartObject;


class ' . $name . 'Facade
{

	use SmartObject;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function save(' . $name . ' $' . Strings::firstLower($name) . ')
	{
		$this->entityManager->persist($' . Strings::firstLower($name) . ');
		$this->entityManager->flush();
	}


	public function remove(' . $name . ' $' . Strings::firstLower($name) . ')
	{
		$this->entityManager->remove($' . Strings::firstLower($name) . ');
		$this->entityManager->flush();
	}

}
'
		);

		// Module/Model/Repositories

		file_put_contents(
			sprintf('%s/Model/Repositories/%sRepository.php', $path, $name),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Model\Repositories;

use ' . $namespace . '\Modules\\' . $name . '\Model\Entities\\' . $name . ';
use Voonne\Model\EntityRepository;


class ' . $name . 'Repository extends EntityRepository
{

	public function getQuery()
	{
		return $this->createQueryBuilder()->select(\'e\')->from(' . $name . '::class, \'e\');
	}

}
'
		);

		// Module/Pages

		file_put_contents(
			sprintf('%s/Pages/CreatePage.php', $path),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Pages;

use Voonne\Pages\Page;


class CreatePage extends Page
{

	public function __construct()
	{
		parent::__construct(\'create\', \'Create ' . Strings::lower($this->getLabel($name)) . '\');
	}


	public function isAuthorized()
	{
		return $this->getUser()->havePrivilege(\'admin\', \'' . Strings::firstLower($name) . '\', \'create\');
	}

}
'
		);

		file_put_contents(
			sprintf('%s/Pages/DefaultPage.php', $path),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Pages;

use Voonne\Pages\Page;


class DefaultPage extends Page
{

	public function __construct()
	{
		parent::__construct(\'default\', \'View ' . Strings::lower($this->getLabel($name)) . '\');
	}


	public function isAuthorized()
	{
		return $this->getUser()->havePrivilege(\'admin\', \'' . Strings::firstLower($name) . '\', \'view\');
	}

}
'
		);

		file_put_contents(
			sprintf('%s/Pages/UpdatePage.php', $path),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Pages;

use ' . $namespace . '\Modules\\' . $name . '\Model\Repositories\\' . $name . 'Repository;
use Voonne\Messages\FlashMessage;
use Voonne\Pages\Page;


class UpdatePage extends Page
{

	/**
	 * @var ' . $name . 'Repository
	 */
	private $' . Strings::firstLower($name) . 'Repository;


	public function __construct(' . $name . 'Repository $' . Strings::firstLower($name) . 'Repository)
	{
		parent::__construct(\'update\', \'Update ' . Strings::lower($this->getLabel($name)) . '\');

		$this->' . Strings::firstLower($name) . 'Repository = $' . Strings::firstLower($name) . 'Repository;

		$this->hideFromMenu();
	}


	public function startup()
	{
		parent::startup();

		if ($this->' . Strings::firstLower($name) . 'Repository->countBy([\'id\' => $this->getPresenter()->getParameter(\'id\')]) == 0) {
			$this->flashMessage(\'' . $this->getLabel($name) . ' was not found.\', FlashMessage::ERROR);
			$this->redirect(\'' . Strings::firstLower($name) . '.default\');
		}
	}


	public function isAuthorized()
	{
		return $this->getUser()->havePrivilege(\'admin\', \'' . Strings::firstLower($name) . '\', \'update\');
	}

}
'
		);

		// Module/Panels

		file_put_contents(
			sprintf('%s/Panels/CreateFormPanel.php', $path),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Panels;

use ' . $namespace . '\Modules\\' . $name . '\Model\Entities\\' . $name . ';
use ' . $namespace . '\Modules\\' . $name . '\Model\Facades\\' . $name . 'Facade;
use Voonne\Forms\Container;
use Voonne\Messages\FlashMessage;
use Voonne\Panels\Panels\FormPanel\FormPanel;


class CreateFormPanel extends FormPanel
{

	/**
	 * @var ' . $name . 'Facade
	 */
	private $' . Strings::firstLower($name) . 'Facade;


	public function __construct(' . $name . 'Facade $' . Strings::firstLower($name) . 'Facade)
	{
		parent::__construct();

		$this->' . Strings::firstLower($name) . 'Facade = $' . Strings::firstLower($name) . 'Facade;

		$this->setTitle(\'' . $this->getLabel($name) . '\');
	}


	public function setupForm(Container $container)
	{
		$container->addSubmit(\'submit\', \'Create ' . Strings::lower($this->getLabel($name)) . '\');

		$container->onSuccess[] = [$this, \'success\'];
	}


	public function success(Container $container, $values)
	{
		$' . Strings::firstLower($name) . ' = new ' . $name . '();

		$this->' .  Strings::firstLower($name) . 'Facade->save($' . Strings::firstLower($name) . ');

		$this->flashMessage(\'' . $this->getLabel($name) . ' was successfully created.\', FlashMessage::SUCCESS);
		$this->redirect(\'' . Strings::firstLower($name) . '.default\');
	}

}
'
		);

		file_put_contents(
			sprintf('%s/Panels/%sTablePanel.php', $path, $name),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Panels;

use ' . $namespace . '\Modules\\' . $name . '\Model\Entities\\' . $name . ';
use ' . $namespace . '\Modules\\' . $name . '\Model\Repositories\\' . $name . 'Repository;
use Voonne\Panels\Panels\TablePanel\Adapters\Doctrine2Adapter;
use Voonne\Panels\Panels\TablePanel\TablePanel;


class ' . $name . 'TablePanel extends TablePanel
{

	/**
	 * @var ' . $name . 'Repository
	 */
	private $' . Strings::firstLower($name) . 'Repository;


	public function __construct(' . $name . 'Repository $' . Strings::firstLower($name) . 'Repository)
	{
		parent::__construct();

		$this->' . Strings::firstLower($name) . 'Repository = $' . Strings::firstLower($name) . 'Repository;

		$this->setTitle(\'' . $this->getLabel($name) . '\');
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->addColumn(\'id\', \'Id\');

		$this->addAction(\'update\', \'Update\', function (' . $name . ' $' . Strings::firstLower($name) . ') {
			if ($this->getUser()->havePrivilege(\'admin\', \'' . Strings::firstLower($name) . '\', \'update\')) {
				return $this->link(\'' . Strings::firstLower($name) . '.update\', [\'id\' => $' . Strings::firstLower($name) . '->getId()]);
			} else {
				return null;
			}
		});

		$this->setAdapter(new Doctrine2Adapter($this->' . Strings::firstLower($name) . 'Repository->getQuery()));
	}

}
'
		);

		file_put_contents(
			sprintf('%s/Panels/UpdateFormPanel.php', $path),
			'<?php

namespace ' . $namespace . '\Modules\\' . $name . '\Panels;

use ' . $namespace . '\Modules\\' . $name . '\Model\Entities\\' . $name . ';
use ' . $namespace . '\Modules\\' . $name . '\Model\Facades\\' . $name . 'Facade;
use ' . $namespace . '\Modules\\' . $name . '\Model\Repositories\\' . $name . 'Repository;
use Voonne\Forms\Container;
use Voonne\Messages\FlashMessage;
use Voonne\Panels\Panels\FormPanel\FormPanel;


class UpdateFormPanel extends FormPanel
{

	/**
	 * @var ' . $name . 'Repository
	 */
	private $' . Strings::firstLower($name) . 'Repository;

	/**
	 * @var ' . $name . 'Facade
	 */
	private $' . Strings::firstLower($name) . 'Facade;

	/**
	 * @var ' . $name . '
	 */
	private $' . Strings::firstLower($name) . ';


	public function __construct(' . $name . 'Repository $' . Strings::firstLower($name) . 'Repository, ' . $name . 'Facade $' . Strings::firstLower($name) . 'Facade)
	{
		parent::__construct();

		$this->' . Strings::firstLower($name) . 'Repository = $' . Strings::firstLower($name) . 'Repository;
		$this->' . Strings::firstLower($name) . 'Facade = $' . Strings::firstLower($name) . 'Facade;

		$this->setTitle(\'' . $this->getLabel($name) . '\');
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->' . Strings::firstLower($name) . ' = $this->' . Strings::firstLower($name) . 'Repository->find($this->getPresenter()->getParameter(\'id\'));
	}


	public function setupForm(Container $container)
	{
		$container->addSubmit(\'submit\', \'Update ' . Strings::lower($this->getLabel($name)) . '\');

		$container->onSuccess[] = [$this, \'success\'];
	}


	public function success(Container $container, $values)
	{
		$this->' . Strings::firstLower($name) . 'Facade->save($this->' . Strings::firstLower($name) . ');

		$this->flashMessage(\'' . $this->getLabel($name) . ' was successfully updated.\', FlashMessage::SUCCESS);
		$this->redirect(\'' . Strings::firstLower($name) . '.default\');
	}

}
'
		);
	}


	private function getLabel($name)
	{
		$result = '';

		foreach(preg_split('/(?=[A-Z])/', $name) as $part) {
			if (!empty($part)) {
				$result .= $part . ' ';
			}
		}

		return Strings::trim($result);
	}

}
