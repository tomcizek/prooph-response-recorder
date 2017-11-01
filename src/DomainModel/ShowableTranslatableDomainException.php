<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\DomainModel;

use Throwable;

interface ShowableTranslatableDomainException extends Throwable
{
	/**
	 * @return array|mixed[]
	 */
	public function getMessageParameters(): array;
}
