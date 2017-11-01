<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection;

class GenericShowableMutationError extends AbstractMutationError
{
	/**
	 * @param string        $errorMessage
	 * @param array|mixed[] $errorMessageParams
	 *
	 * @return GenericShowableMutationError
	 */
	public static function create(string $errorMessage, array $errorMessageParams = []): self
	{
		return new self($errorMessage, $errorMessageParams);
	}
}
