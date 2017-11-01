<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection;

interface ShowableMutationError
{
	public function getErrorMessage(): string;

	/**
	 * @return array|mixed[]
	 */
	public function getErrorMessageParams(): array;
}
