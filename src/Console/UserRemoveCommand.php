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
use Voonne\Voonne\Model\Facades\UserFacade;
use Voonne\Voonne\Model\Repositories\UserRepository;


class UserRemoveCommand extends Command
{

	/**
	 * @var UserRepository
	 * @inject
	 */
	public $userRepository;

	/**
	 * @var UserFacade
	 * @inject
	 */
	public $userFacade;

	/**
	 * @var string
	 */
	private $name = 'voonne:user:remove';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Removes the user.');

		$this->setDefinition([
			new InputArgument('email', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$email = $input->getArgument('email');

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<error>  The Voonne Platform must be installed in the first place. Please use command voonne:install.  </error>');

			return 1;
		}

		try {
			$user = $this->userRepository->findOneBy(['email' => $email]);
		} catch (IOException $e) {
			$output->writeln('<error>  User with this email was not found.  </error>');

			return 1;
		}

		try {
			$this->userFacade->remove($user);

			$output->writeln('The user has been successfully removed.');

			return 0;
		} catch (PDOException $e) {
			$output->writeln(sprintf('<error>  %s  </error>', $e->getMessage()));

			return 1;
		}
	}

}
