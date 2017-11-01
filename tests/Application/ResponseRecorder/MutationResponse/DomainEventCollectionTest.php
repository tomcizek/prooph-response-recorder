<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Tests\Application\ResponseRecorder\MutationResponse;

use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\DomainEvent;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\DomainEventCollection\MoreThanOneEventOfRequestedTypeExists;
use TomCizek\ResponseRecorder\Tests\FakeImplementations\TestDomainEvent;
use TomCizek\ResponseRecorder\Tests\FakeImplementations\TestTranslatableDomainErrorDomainEvent;

class DomainEventCollectionTest extends TestCase
{
	/** @var DomainEventCollection */
	private $domainEventCollection;

	public function testGetDomainEventsOfType_WhenNoEvents_ShouldReturnEmptyArray()
	{
		$this->givenCollectionWithEvents([]);

		$result = $this->whenGetEventsOfType(TestDomainEvent::class);

		self::assertSame([], $result);
	}

	public function testGetDomainEventsOfType_WhenTwoEventsPerEachType_ShouldReturnTwoEvents()
	{
		$this->givenCollectionWithEvents(
			[
				new TestDomainEvent([]),
				new TestDomainEvent([]),
				new TestTranslatableDomainErrorDomainEvent([]),
				new TestTranslatableDomainErrorDomainEvent([]),
			]
		);
		$result = $this->whenGetEventsOfType(TestTranslatableDomainErrorDomainEvent::class);

		self::assertCount(2, $result);
	}

	public function testGetDomainEventOfType_WhenNoEvents_ShouldReturnNull()
	{
		$this->givenCollectionWithEvents([]);

		$result = $this->whenGetEventOfType(TestDomainEvent::class);

		self::assertNull($result);
	}

	public function testGetDomainEventOfType_WhenTwoEvents_ShouldThrowMoreThanOneEventOfRequestedTypeExists()
	{
		$this->givenCollectionWithEvents(
			[
				new TestDomainEvent([]),
				new TestDomainEvent([]),
			]
		);
		$this->expectException(MoreThanOneEventOfRequestedTypeExists::class);

		$this->whenGetEventOfType(TestDomainEvent::class);
	}

	public function testGetDomainEventOfType_WhenOneEvent_ShouldReturnEvent()
	{
		$this->givenCollectionWithEvents(
			[
				new TestDomainEvent([]),
			]
		);
		$result = $this->whenGetEventOfType(TestDomainEvent::class);

		self::assertInstanceOf(TestDomainEvent::class, $result);
	}

	private function givenCollectionWithEvents(array $events): void
	{
		$this->domainEventCollection = new DomainEventCollection($events);
	}

	private function whenGetEventsOfType(string $type): array
	{
		return $this->domainEventCollection->getDomainEventsOfType($type);
	}

	private function whenGetEventOfType(string $type): ?DomainEvent
	{
		return $this->domainEventCollection->getDomainEventOfType($type);
	}
}
