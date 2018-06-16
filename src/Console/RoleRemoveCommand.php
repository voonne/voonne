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
use Voonne\Voonne\Model\Facades\RoleFacade;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class RoleRemoveCommand extends Command
{

	/**
	 * @var RoleRepository
	 * @inject
	 */
	public $roleRepository;

	/**
	 * @var RoleFacade
	 * @inject
	 */
	public $roleFacade;

	/**
	 * @var string
	 */
	private $name = 'voonne:role:remove';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Removes the role.');

		$this->setDefinition([
			new InputArgument('name', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$roleName = $input->getArgument('name');

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<fg=red>The Voonne Platform must be installed in the first place. Please use command voonne:install.</>');

			return 1;
		}

		try {
			$role = $this->roleRepository->findOneBy(['name' => $roleName]);
		} catch (IOException $e) {
			$output->writeln('<fg=red>Role with this name was not found.</>');

			return 1;
		}

		try {
			$this->roleFacade->remove($role);

			$output->writeln('The role has been successfully removed.');

			return 0;
		} catch (PDOException $e) {
			$output->writeln(sprintf('<fg=red>%s</>', $e->getMessage()));

			return 1;
		}
	}

}
