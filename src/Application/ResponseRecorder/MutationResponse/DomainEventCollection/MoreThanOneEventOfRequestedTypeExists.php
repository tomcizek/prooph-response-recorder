<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection;

use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\InvalidManipulationWithResponseException;

class MoreThanOneEventOfRequestedTypeExists extends InvalidManipulationWithResponseException
{
}
