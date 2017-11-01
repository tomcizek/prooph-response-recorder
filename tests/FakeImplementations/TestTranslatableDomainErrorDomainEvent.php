<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Tests\FakeImplementations;

use Prooph\Common\Messaging\DomainEvent;
use TomCizek\ResponseRecorder\DomainModel\TranslatableDomainErrorEvent;

class TestTranslatableDomainErrorDomainEvent extends DomainEvent implements TranslatableDomainErrorEvent
{
	public const TEST_ERROR_MESSAGE = 'test_error';
	public const TEST_ERROR_MESSAGE_PARAMS = ['foo' => 'bar'];

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

	public function getErrorMessage(): string
	{
		return self::TEST_ERROR_MESSAGE;
	}

	/**
	 * @return array|mixed[]
	 */
	public function getErrorMessageParams(): array
	{
		return self::TEST_ERROR_MESSAGE_PARAMS;
	}
}
