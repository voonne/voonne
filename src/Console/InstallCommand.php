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
use Doctrine\ORM\Tools\SchemaValidator;
use Kdyby\Doctrine\Tools\CacheCleaner;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Voonne\Domains\DomainManager;
use Voonne\Voonne\Model\Entities\Language;
use Voonne\Voonne\Model\Repositories\LanguageRepository;


class InstallCommand extends Command
{

	/**
	 * @var CacheCleaner
	 * @inject
	 */
	public $cacheCleaner;

	/**
	 * @var EntityManagerInterface
	 * @inject
	 */
	public $entityManager;

	/**
	 * @var LanguageRepository
	 * @inject
	 */
	public $languageRepository;

	/**
	 * @var DomainManager
	 * @inject
	 */
	public $domainManager;

	/**
	 * @var string
	 */
	private $name = 'voonne:install';


	protected function configure()
	{
		$this->setName($this->name);
		$this->setDescription('Installs the Voonne Platform.');
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->cacheCleaner->invalidate();

		$validator = new SchemaValidator($this->entityManager);

		if(!$validator->schemaInSyncWithMetadata()) {
			$output->writeln('<error> The database schema is not in sync with the current mapping file. </error>');
			return 1;
		}

		if($this->languageRepository->countBy([]) != 0) {
			$output->writeln('<error>The Voonne Platform is already installed.</error>');
			return 1;
		}

		try {
			foreach ($this->getLanguages() as $language) {
				$this->entityManager->persist($language);
			}

			$this->entityManager->flush();

			$this->domainManager->synchronize();

			$output->writeln('<info>The Voonne Platform has been successfully installed.</info>');

			return 1;
		} catch (PDOException $e) {
			$output->writeln('<error>error!</error>');

			return 0;
		}
	}


	/**
	 * @return array
	 */
	private function getLanguages()
	{
		return [
			new Language('Abkhaz', 'ab'),
			new Language('Afar', 'aa'),
			new Language('Afrikaans', 'af'),
			new Language('Akan', 'ak'),
			new Language('Albanian', 'sq'),
			new Language('Amharic', 'am'),
			new Language('Arabic', 'ar'),
			new Language('Aragonese', 'an'),
			new Language('Armenian', 'hy'),
			new Language('Assamese', 'as'),
			new Language('Avaric', 'av'),
			new Language('Avestan', 'ae'),
			new Language('Aymara', 'ay'),
			new Language('Azerbaijani', 'az'),
			new Language('Bambara', 'bm'),
			new Language('Bashkir', 'ba'),
			new Language('Basque', 'eu'),
			new Language('Belarusian', 'be'),
			new Language('Bengali', 'bn'),
			new Language('Bihari', 'bh'),
			new Language('Bislama', 'bi'),
			new Language('Bosnian', 'bs'),
			new Language('Breton', 'br'),
			new Language('Bulgarian', 'bg'),
			new Language('Burmese', 'my'),
			new Language('Catalan', 'ca'),
			new Language('Chamorro', 'ch'),
			new Language('Chechen', 'ce'),
			new Language('Chichewa', 'ny'),
			new Language('Chinese', 'zh'),
			new Language('Chuvash', 'cv'),
			new Language('Cornish', 'kw'),
			new Language('Corsican', 'co'),
			new Language('Cree', 'cr'),
			new Language('Croatian', 'hr'),
			new Language('Czech', 'cs'),
			new Language('Danish', 'da'),
			new Language('Divehi', 'dv'),
			new Language('Dutch', 'nl'),
			new Language('Dzongkha', 'dz'),
			new Language('English', 'en'),
			new Language('Esperanto', 'eo'),
			new Language('Estonian', 'et'),
			new Language('Ewe', 'ee'),
			new Language('Faroese', 'fo'),
			new Language('Fijian', 'fj'),
			new Language('Finnish', 'fi'),
			new Language('French', 'fr'),
			new Language('Fula', 'ff'),
			new Language('Galician', 'gl'),
			new Language('Georgian', 'ka'),
			new Language('German', 'de'),
			new Language('Greek', 'el'),
			new Language('Guaraní', 'gn'),
			new Language('Gujarati', 'gu'),
			new Language('Haitian', 'ht'),
			new Language('Hausa', 'ha'),
			new Language('Hebrew', 'he'),
			new Language('Herero', 'hz'),
			new Language('Hindi', 'hi'),
			new Language('Hiri Motu', 'ho'),
			new Language('Hungarian', 'hu'),
			new Language('Interlingua', 'ia'),
			new Language('Indonesian', 'id'),
			new Language('Interlingue', 'ie'),
			new Language('Irish', 'ga'),
			new Language('Igbo', 'ig'),
			new Language('Inupiaq' ,'ik'),
			new Language('Ido', 'io'),
			new Language('Icelandic', 'is'),
			new Language('Italian', 'it'),
			new Language('Inuktitut', 'iu'),
			new Language('Japanese', 'ja'),
			new Language('Javanese', 'jv'),
			new Language('Kalaallisut', 'kl'),
			new Language('Kannada', 'kn'),
			new Language('Kanuri', 'kr'),
			new Language('Kashmiri', 'ks'),
			new Language('Kazakh', 'kk'),
			new Language('Khmer', 'km'),
			new Language('Kikuyu', 'ki'),
			new Language('Kinyarwanda', 'rw'),
			new Language('Kyrgyz', 'ky'),
			new Language('Komi', 'kv'),
			new Language('Kongo', 'kg'),
			new Language('Korean', 'ko'),
			new Language('Kurdish', 'ku'),
			new Language('Kwanyama', 'kj'),
			new Language('Latin', 'la'),
			new Language('Luxembourgish', 'lb'),
			new Language('Ganda', 'lg'),
			new Language('Limburgish', 'li'),
			new Language('Lingala', 'ln'),
			new Language('Lao', 'lo'),
			new Language('Lithuanian', 'lt'),
			new Language('Luba-Katanga', 'lu'),
			new Language('Latvian', 'lv'),
			new Language('Manx', 'gv'),
			new Language('Macedonian', 'mk'),
			new Language('Malagasy', 'mg'),
			new Language('Malay', 'ms'),
			new Language('Malayalam', 'ml'),
			new Language('Maltese', 'mt'),
			new Language('Māori', 'mi'),
			new Language('Marathi (Marāṭhī)', 'mr'),
			new Language('Marshallese', 'mh'),
			new Language('Mongolian', 'mn'),
			new Language('Nauruan', 'na'),
			new Language('Navajo', 'nv'),
			new Language('Northern Ndebele', 'nd'),
			new Language('Nepali', 'ne'),
			new Language('Ndonga', 'ng'),
			new Language('Norwegian Bokmål', 'nb'),
			new Language('Norwegian Nynorsk', 'nn'),
			new Language('Norwegian', 'no'),
			new Language('Nuosu', 'ii'),
			new Language('Southern Ndebele', 'nr'),
			new Language('Occitan', 'oc'),
			new Language('Ojibwe', 'oj'),
			new Language('Church Slavonic', 'cu'),
			new Language('Oromo', 'om'),
			new Language('Oriya', 'or'),
			new Language('Ossetian', 'os'),
			new Language('Panjabi', 'pa'),
			new Language('Pāli', 'pi'),
			new Language('Persian', 'fa'),
			new Language('Polish', 'pl'),
			new Language('Pashto', 'ps'),
			new Language('Portuguese', 'pt'),
			new Language('Quechua', 'qu'),
			new Language('Romansh', 'rm'),
			new Language('Kirundi', 'rn'),
			new Language('Reunionese', 'rc'),
			new Language('Romanian', 'ro'),
			new Language('Russian', 'ru'),
			new Language('Sanskrit (Saṁskṛta)', 'sa'),
			new Language('Sardinian', 'sc'),
			new Language('Sindhi', 'sd'),
			new Language('Northern Sami', 'se'),
			new Language('Samoan', 'sm'),
			new Language('Sango', 'sg'),
			new Language('Serbian', 'sr'),
			new Language('Scottish Gaelic', 'gd'),
			new Language('Shona', 'sn'),
			new Language('Sinhalese', 'si'),
			new Language('Slovak', 'sk'),
			new Language('Slovene', 'sl'),
			new Language('Somali', 'so'),
			new Language('Southern Sotho', 'st'),
			new Language('Spanish', 'es'),
			new Language('Sundanese', 'su'),
			new Language('Swahili', 'sw'),
			new Language('Swati', 'ss'),
			new Language('Swedish', 'sv'),
			new Language('Tamil', 'ta'),
			new Language('Telugu', 'te'),
			new Language('Tajik', 'tg'),
			new Language('Thai', 'th'),
			new Language('Tigrinya', 'ti'),
			new Language('Tibetan Standard', 'bo'),
			new Language('Turkmen', 'tk'),
			new Language('Tagalog', 'tl'),
			new Language('Tswana', 'tn'),
			new Language('Tonga', 'to'),
			new Language('Turkish', 'tr'),
			new Language('Tsonga', 'ts'),
			new Language('Tatar', 'tt'),
			new Language('Twi', 'tw'),
			new Language('Tahitian', 'ty'),
			new Language('Uyghur', 'ug'),
			new Language('Ukrainian', 'uk'),
			new Language('Urdu', 'ur'),
			new Language('Uzbek', 'uz'),
			new Language('Venda', 've'),
			new Language('Vietnamese', 'vi'),
			new Language('Volapük', 'vo'),
			new Language('Walloon', 'wa'),
			new Language('Welsh', 'cy'),
			new Language('Wolof', 'wo'),
			new Language('Western Frisian', 'fy'),
			new Language('Xhosa', 'xh'),
			new Language('Yiddish', 'yi'),
			new Language('Yoruba', 'yo'),
			new Language('Zhuang', 'za'),
			new Language('Zulu', 'zu')
		];
	}

}
