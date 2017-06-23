<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class RoleListCommand extends Command
{

	/**
	 * @var RoleRepository
	 * @inject
	 */
	public $roleRepository;

	/**
	 * @var string
	 */
	private $name = 'voonne:role:list';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Shows a list of roles.');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<error>  The Voonne Platform must be installed in the first place. Please use command voonne:install.  </error>');
			return 1;
		}

		foreach ($this->roleRepository->findAll() as $role) {
			/** @var Role $role */
			$output->writeln(sprintf('<fg=yellow>%s</>', $role->getName()));

			foreach ($this->getResources($role->getPrivileges()) as $role => $items) {
				$privileges = implode(', ', $items);

				$output->writeln(sprintf('  <fg=green>%s</>%s%s', $role, str_repeat(' ', (35 - strlen($role))), $privileges));
			}
		}
	}


	private function getResources($privileges)
	{
		$roles = [];

		foreach ($privileges as $privilege) {
			/** @var Privilege $privilege */
			$roles[$privilege->getResource()->getName()][] = $privilege->getName();
		}

		foreach ($roles as $role => $items) {
			asort($roles[$role]);
		}

		return $roles;
	}

}
