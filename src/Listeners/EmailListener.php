<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne\Listeners;

use Kdyby\Events\Subscriber;
use Nette\Application\LinkGenerator;
use Nette\Localization\ITranslator;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Voonne\Voonne\Model\Entities\LostPassword;
use Voonne\Voonne\Model\Facades\LostPasswordFacade;


class EmailListener implements Subscriber
{

	/**
	 * @var ITranslator
	 */
	private $translator;

	/**
	 * @var IMailer
	 */
	private $mailer;

	/**
	 * @var LinkGenerator
	 */
	private $linkGenerator;


	public function __construct(ITranslator $translator, IMailer $mailer, LinkGenerator $linkGenerator)
	{
		$this->translator = $translator;
		$this->mailer = $mailer;
		$this->linkGenerator = $linkGenerator;
	}


	function getSubscribedEvents()
	{
		return [
			LostPasswordFacade::class . '::onCreate' => 'onCreateLostPassword'
		];
	}


	public function onCreateLostPassword(LostPassword $lostPassword)
	{
		$message = new Message();
		$message->addTo($lostPassword->getUser()->getEmail());
		$message->setSubject($this->translator->translate('voonne-mail.lostPassword.title'));

		$link = $this->linkGenerator->link('Admin:Default:newPassword', ['code' => $lostPassword->getCode()]);

		$message->setHtmlBody(
			'<p>' . $this->translator->translate('voonne-mail.lostPassword.p1') . '</p>' .
			'<p>' . $this->translator->translate('voonne-mail.lostPassword.p2') . '</p>' .
			'<p>' . $this->translator->translate('voonne-mail.lostPassword.p3') . '</p>' .
			'<p><a href="' . $link . '">' . $link . '</a></p>');

		$this->mailer->send($message);
	}

}
