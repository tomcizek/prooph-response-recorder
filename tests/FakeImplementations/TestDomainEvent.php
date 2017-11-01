<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Tests\FakeImplementations;

use Prooph\Common\Messaging\DomainEvent;

class TestDomainEvent extends DomainEvent
{
	/** @var array */
	private $payload;

	public function __construct(array $payload)
	{
		$this->init();
		$this->payload = $payload;
	}

	protected function setPayload(array $payload): void
	{
		$this->payload = $payload;
	}

	public function payload(): array
	{
		return $this->payload;
	}
}
