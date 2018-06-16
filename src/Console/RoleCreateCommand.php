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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Facades\RoleFacade;


class RoleCreateCommand extends Command
{

	/**
	 * @var RoleFacade
	 * @inject
	 */
	public $roleFacade;

	/**
	 * @var string
	 */
	private $name = 'voonne:role:create';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Creates the new role.');

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
			$this->roleFacade->save(new Role($roleName));

			$output->writeln('The new role was created successfully.');

			return 0;
		} catch (DuplicateEntryException $e) {
			$output->writeln(sprintf('<fg=red>%s</>', $e->getMessage()));

			return 1;
		}
	}

}
