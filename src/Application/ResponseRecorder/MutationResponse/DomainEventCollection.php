<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse;

use Countable;
use Prooph\Common\Messaging\DomainEvent;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection\MoreThanOneEventOfRequestedTypeExists;

class DomainEventCollection implements Countable
{
	/** @var DomainEvent[] */
	protected $domainEvents = [];

	/**
	 * @param DomainEvent[] $domainEvents
	 */
	public function __construct(array $domainEvents)
	{
		$this->domainEvents = $domainEvents;
	}

	/**
	 * @return DomainEvent[]
	 */
	public function getAll(): array
	{
		return $this->domainEvents;
	}

	public function getDomainEventOfType(string $type): ?DomainEvent
	{
		$ofType = $this->getDomainEventsOfType($type);

		if (count($ofType) > 1) {
			throw new MoreThanOneEventOfRequestedTypeExists(
				sprintf('More than one event of requested type %s exists.', $type)
			);
		}
		if (isset($ofType[0])) {
			return $ofType[0];
		}

		return null;
	}

	/**
	 * @param string $type
	 *
	 * @return DomainEvent[]
	 */
	public function getDomainEventsOfType(string $type): array
	{
		$ofType = [];
		foreach ($this->domainEvents as $event) {
			if (is_a($event, $type)) {
				$ofType[] = $event;
			}
		}

		return $ofType;
	}

	public function count(): int
	{
		return count($this->domainEvents);
	}
}


