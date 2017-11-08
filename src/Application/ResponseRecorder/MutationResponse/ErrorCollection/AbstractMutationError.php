<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection;

abstract class AbstractMutationError implements ShowableMutationError
{
	/** @var string */
	protected $errorMessage;

	/** @var array|mixed[] */
	protected $errorMessageParams;

	/**
	 * @param string        $errorMessage
	 * @param array|mixed[] $errorMessageParams
	 */
	protected function __construct(string $errorMessage, array $errorMessageParams = [])
	{
		$this->errorMessage = $errorMessage;
		$this->errorMessageParams = $errorMessageParams;
	}

	public function getErrorMessage(): string
	{
		return $this->errorMessage;
	}

	/**
	 * @return array|mixed[]
	 */
	public function getErrorMessageParams(): array
	{
		return $this->errorMessageParams;
	}
}
