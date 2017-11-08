<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection\ShowableMutationError;

class MutationErrorCollection implements Countable, IteratorAggregate
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

	/**
	 * @return Iterator|ShowableMutationError[]
	 */
	public function getIterator(): Iterator
	{
		return new ArrayIterator($this->errors);
	}
}
