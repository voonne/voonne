<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use Doctrine\ORM\EntityManagerInterface;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Model\IOException;
use Voonne\Voonne\Model\Entities\Domain;
use Voonne\Voonne\Model\Entities\DomainLanguage;
use Voonne\Voonne\Model\Repositories\DomainRepository;
use Voonne\Voonne\Model\Repositories\LanguageRepository;


class DomainCreateCommand extends Command
{

	/**
	 * @var DomainRepository
	 * @inject
	 */
	public $domainRepository;

	/**
	 * @var LanguageRepository
	 * @inject
	 */
	public $languageRepository;

	/**
	 * @var EntityManagerInterface
	 * @inject
	 */
	public $entityManager;

	/**
	 * @var string
	 */
	private $name = 'voonne:domain:create';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Adds new domain.');

		$this->setDefinition([
			new InputArgument('domain', InputArgument::REQUIRED),
			new InputArgument('language', InputArgument::REQUIRED)
		]);
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$domainArgument = $input->getArgument('domain');
		$languageArgument = $input->getArgument('language');

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<error> The Voonne Platform must be installed in the first place. Please use command voonne:install. </error>');
			return 1;
		}

		if ($this->domainRepository->countBy(['name' => $domainArgument]) != 0) {
			$output->writeln('<error> This domain already exists. </error>');

			return 1;
		}

		try {
			$language = $this->languageRepository->findOneBy(['isoCode' => $languageArgument]);
		} catch (IOException $e) {
			$output->writeln('<error> Domain language must be valid ISO code. </error>');

			return 1;
		}

		try {
			$this->entityManager->persist(new DomainLanguage(new Domain($domainArgument), $language));
			$this->entityManager->flush();

			$output->writeln('<info> The new user was created successfully. </info>');

			return 0;
		} catch (PDOException $e) {
			$output->writeln('<error> error! </error>');

			return 1;
		}
	}

}
