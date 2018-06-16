<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file LICENCE.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Console;

use Kdyby\Translation\Translator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Resource;
use Voonne\Voonne\Model\Entities\Zone;
use Voonne\Voonne\Model\Repositories\ZoneRepository;


class PermissionListCommand extends Command
{

	/**
	 * @var ZoneRepository
	 * @inject
	 */
	public $zoneRepository;

	/**
	 * @var Translator
	 * @inject
	 */
	public $translator;

	/**
	 * @var string
	 */
	private $name = 'voonne:permission:list';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Shows a list of permissions.');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->translator->setLocale('en');

		if(!$this->getHelper('state')->isInstalled()) {
			$output->writeln('<fg=red>The Voonne Platform must be installed in the first place. Please use command voonne:install.</>');

			return 1;
		}

		$zones = $this->zoneRepository->findAll();

		if (count($zones) == 0) {
			$output->writeln('There are no permissions. You can create a new permissions by installing existing modules or creates your own modules.');
		}

		foreach ($zones as $zone) {
			/** @var Zone $zone */
			$output->writeln(sprintf(
				'<fg=yellow>%s</>%s%s',
				$zone->getName(),
				str_repeat(' ', (37 - strlen($zone->getName()))),
				$this->translator->translate($zone->getDescription())
			));

			foreach ($zone->getResources() as $resource) {
				/** @var Resource $resource */
				$output->writeln(sprintf(
					'  <fg=yellow>%s</>%s%s',
					$resource->getName(),
					str_repeat(' ', (35 - strlen($resource->getName()))),
					$this->translator->translate($resource->getDescription())
				));

				foreach ($resource->getPrivileges() as $privilege) {
					/** @var Privilege $privilege */
					$output->writeln(sprintf(
						'    <fg=green>%s</>%s%s',
						$privilege->getName(),
						str_repeat(' ', (33- strlen($privilege->getName()))),
						$this->translator->translate($privilege->getDescription())
					));
				}
			}
		}

		return 0;
	}

}
