<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder;

use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\MutationErrorCollection;

final class BasicMutationResponse extends AbstractMutationResponse
{
	private function __construct()
	{
	}

	public static function create(
		DomainEventCollection $aggregateChangedEvents,
		MutationErrorCollection $errors
	): BasicMutationResponse {
		$instance = new self();
		$instance->domainEvents = $aggregateChangedEvents;
		$instance->errors = $errors;

		return $instance;
	}
}
