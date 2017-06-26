<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Model\IOException;
use Voonne\Security\Authorizator;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Facades\RoleFacade;
use Voonne\Voonne\Model\Repositories\PrivilegeRepository;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class RoleAddPrivilegeCommand extends Command
{

	/**
	 * @var RoleRepository
	 * @inject
	 */
	public $roleRepository;

	/**
	 * @var PrivilegeRepository
	 * @inject
	 */
	public $privilegeRepository;

	/**
	 * @var RoleFacade
	 * @inject
	 */
	public $roleFacade;

	/**
	 * @var IStorage
	 * @inject
	 */
	public $storage;

	/**
	 * @var string
	 */
	private $name = 'voonne:role:add-privilege';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Adds the privilege to the role.');

		$this->setDefinition([
			new InputArgument('role', InputArgument::REQUIRED),
			new InputArgument('zone', InputArgument::REQUIRED),
			new InputArgument('resource', InputArgument::REQUIRED),
			new InputArgument('privilege', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$roleName = $input->getArgument('role');
		$zoneName = $input->getArgument('zone');
		$resourceName = $input->getArgument('resource');
		$privilegeName = $input->getArgument('privilege');

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<error>  The Voonne Platform must be installed in the first place. Please use command voonne:install.  </error>');

			return 1;
		}

		try {
			/** @var Role $role */
			$role = $this->roleRepository->findOneBy(['name' => $roleName]);
		} catch (IOException $e) {
			$output->writeln('<error>  Role with this name was not found.  </error>');

			return 1;
		}

		try {
			/** @var Privilege $privilege */
			$privilege = $this->privilegeRepository->getPrivilege($zoneName, $resourceName, $privilegeName);
		} catch (IOException $e) {
			$output->writeln('<error>  Privilege with this name was not found.  </error>');

			return 1;
		}

		if ($role->getPrivileges()->contains($privilege)) {
			$output->writeln('<error>  This role already has this privilege.  </error>');

			return 1;
		}

		try {
			$role->addPrivilege($privilege);

			$this->roleFacade->save($role);

			(new Cache($this->storage, Authorizator::CACHE_NAMESPACE))->remove('permissions');

			$output->writeln('The privilege was successfully added to the role.');

			return 0;
		} catch (PDOException $e) {
			$output->writeln(sprintf('<error>  %s  </error>', $e->getMessage()));

			return 1;
		}
	}

}
