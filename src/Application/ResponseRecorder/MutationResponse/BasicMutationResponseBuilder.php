<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse;

use Prooph\Common\Messaging\DomainEvent;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\BasicMutationResponse;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection\GenericShowableMutationError;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection\ShowableMutationError;
use TomCizek\ResponseRecorder\DomainModel\TranslatableDomainErrorEvent;

class BasicMutationResponseBuilder
{
	/** @var DomainEvent[] */
	private $domainEvents = [];

	/** @var ShowableMutationError[] */
	private $errors = [];

	private function __construct()
	{
	}

	public static function create(): self
	{
		return new self();
	}

	public function build(): BasicMutationResponse
	{
		return BasicMutationResponse::create(
			new DomainEventCollection($this->domainEvents),
			new MutationErrorCollection($this->errors)
		);
	}

	public function addDomainEvent(DomainEvent $domainEvent): void
	{
		$this->domainEvents[] = $domainEvent;

		if ($domainEvent instanceof TranslatableDomainErrorEvent) {
			$this->errors[] = GenericShowableMutationError::create(
				$domainEvent->getErrorMessage(),
				$domainEvent->getErrorMessageParams()
			);
		}
	}

	public function addError(ShowableMutationError $showableMutationError): void
	{
		$this->errors[] = $showableMutationError;
	}
}
