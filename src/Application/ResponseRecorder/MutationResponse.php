<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder;

use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\MutationErrorCollection;

interface MutationResponse
{
	public function getDomainEvents(): DomainEventCollection;

	public function getErrors(): MutationErrorCollection;
}
