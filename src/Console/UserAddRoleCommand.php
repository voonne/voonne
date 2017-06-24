<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Model\IOException;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\RoleRepository;
use Voonne\Voonne\Model\Repositories\UserRepository;


class UserAddRoleCommand extends Command
{

	/**
	 * @var UserRepository
	 * @inject
	 */
	public $userRepository;

	/**
	 * @var RoleRepository
	 * @inject
	 */
	public $roleRepository;

	/**
	 * @var UserFacade
	 * @inject
	 */
	public $userFacade;

	/**
	 * @var string
	 */
	private $name = 'voonne:user:add-role';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Adds role to the user.');

		$this->setDefinition([
			new InputArgument('user', InputArgument::REQUIRED),
			new InputArgument('role', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$userEmail = $input->getArgument('user');
		$roleName = $input->getArgument('role');

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<error>  The Voonne Platform must be installed in the first place. Please use command voonne:install.  </error>');

			return 1;
		}

		try {
			/** @var User $user */
			$user = $this->userRepository->findOneBy(['email' => $userEmail]);
		} catch (IOException $e) {
			$output->writeln('<error>  A user with this email was not found.  </error>');

			return 1;
		}

		try {
			/** @var Role $role */
			$role = $this->roleRepository->findOneBy(['name' => $roleName]);
		} catch (IOException $e) {
			$output->writeln('<error>  A role with this name was not found.  </error>');

			return 1;
		}

		if ($user->getRoles()->contains($role)) {
			$output->writeln('<error>  This user already has this role.  </error>');

			return 1;
		}

		try {
			$user->addRole($role);

			$this->userFacade->save($user);

			$output->writeln('The role was successfully added to the user.');

			return 0;
		} catch (PDOException $e) {
			$output->writeln(sprintf('<error>  %s  </error>', $e->getMessage()));

			return 1;
		}
	}

}
