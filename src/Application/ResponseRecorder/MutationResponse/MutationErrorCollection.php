<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse;

use Countable;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection\ShowableMutationError;

class MutationErrorCollection implements Countable
{
	/** @var ShowableMutationError[] */
	protected $errors = [];

	/**
	 * @param ShowableMutationError[] $errorDomainEvents
	 */
	public function __construct(array $errorDomainEvents)
	{
		$this->errors = $errorDomainEvents;
	}

	public function count(): int
	{
		return count($this->errors);
	}

	/**
	 * @return ShowableMutationError[]
	 */
	public function getAll(): array
	{
		return $this->errors;
	}
}
