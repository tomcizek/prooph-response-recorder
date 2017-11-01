<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder;

use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\MutationErrorCollection;

abstract class AbstractMutationResponse implements MutationResponse
{
	/** @var DomainEventCollection */
	protected $domainEvents;

	/** @var MutationErrorCollection */
	protected $errors;

	public function getDomainEvents(): DomainEventCollection
	{
		return $this->domainEvents;
	}

	public function getErrors(): MutationErrorCollection
	{
		return $this->errors;
	}
}
