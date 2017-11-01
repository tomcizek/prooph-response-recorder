<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\DomainModel;

interface TranslatableDomainErrorEvent
{
	public function getErrorMessage(): string;

	/**
	 * @return array|mixed[]
	 */
	public function getErrorMessageParams(): array;
}
