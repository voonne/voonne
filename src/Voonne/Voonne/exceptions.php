<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Voonne;


// inspired by: https://github.com/nette/web-addons.nette.org/blob/master/app/misc/exceptions.php

class InvalidStateException extends \RuntimeException
{

}


class DuplicateEntryException extends \RuntimeException
{

}


class IOException extends \RuntimeException
{

}


class FileNotFoundException extends IOException
{

}


class DirectoryNotFoundException extends IOException
{

}


class AuthorizationException extends IOException
{

}


class InvalidArgumentException extends \InvalidArgumentException
{

}


class ArgumentOutOfRangeException extends InvalidArgumentException
{

}


class NotImplementedException extends \LogicException
{

}


class NotSupportedException extends \LogicException
{

}


class DeprecatedException extends NotSupportedException
{

}


class StaticClassException extends \LogicException
{

}

class InvalidDestinationException extends \Exception
{

}
