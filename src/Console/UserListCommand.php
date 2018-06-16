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
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Repositories\UserRepository;


class UserListCommand extends Command
{

	/**
	 * @var UserRepository
	 * @inject
	 */
	public $userRepository;

	/**
	 * @var string
	 */
	private $name = 'voonne:user:list';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Shows a list of users.');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<fg=red>The Voonne Platform must be installed in the first place. Please use command voonne:install.</>');

			return 1;
		}

		$users = $this->userRepository->findAll();

		if (count($users) == 0) {
			$output->writeln('There are no users. You can create a new user by using voonne:user:create command.');
		}

		foreach ($users as $user) {
			/** @var User $user */
			$roles = $this->getRoles($user->getRoles());

			$output->writeln(sprintf(
				'<fg=yellow>%s</>%s%s',
				$user->getEmail(),
				str_repeat(' ', (37 - strlen($user->getEmail()))),
				empty($roles) ? 'No assigned roles' : implode(', ', $roles)
			));
		}

		return 0;
	}


	private function getRoles($roles)
	{
		$result = [];

		foreach ($roles as $role) {
			/** @var Role $role */
			$result[] = $role->getName();
		}

		asort($result);

		return $result;
	}

}
