<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use Nette\Utils\Validators;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\User;
use Voonne\Voonne\Model\Facades\UserFacade;


class UserCreateCommand extends Command
{

	/**
	 * @var UserFacade
	 * @inject
	 */
	public $userFacade;

	/**
	 * @var string
	 */
	private $name = 'voonne:user:create';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Creates the new user.');

		$this->setDefinition([
			new InputArgument('email', InputArgument::REQUIRED),
			new InputArgument('password', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$userEmail = $input->getArgument('email');
		$userPassword = $input->getArgument('password');

		if(!Validators::isEmail($userEmail)) {
			$output->writeln(sprintf('<error>  First parameter must be valid email, "%s" given.  </error>', $userEmail));

			return 1;
		}

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<error>  The Voonne Platform must be installed in the first place. Please use command voonne:install.  </error>');

			return 1;
		}

		try {
			$this->userFacade->save(new User($userEmail, $userPassword));

			$output->writeln('The new user was created successfully.');

			return 0;
		} catch (DuplicateEntryException $e) {
			$output->writeln(sprintf('<error>  %s  </error>', $e->getMessage()));

			return 1;
		}
	}

}
