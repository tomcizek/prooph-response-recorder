<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Tests\Application;

use Closure;
use Exception;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Psr\Log\NullLogger;
use TomCizek\ResponseRecorder\Application\ResponseRecorder;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\BasicMutationResponse;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection\ShowableMutationError;
use TomCizek\ResponseRecorder\Tests\FakeImplementations\TestDomainEvent;
use TomCizek\ResponseRecorder\Tests\FakeImplementations\TestShowableTranslatableException;
use TomCizek\ResponseRecorder\Tests\FakeImplementations\TestTranslatableDomainErrorDomainEvent;

class ResponseRecorderTest extends TestCase
{
	/** @var ResponseRecorder */
	private $sut;

	/** @var EventBus */
	private $eventBus;

	public function setUp(): void
	{
		parent::setUp();

		$this->eventBus = new EventBus();

		$this->sut = new ResponseRecorder($this->eventBus, new NullLogger());

	}

	public function testRecordOn_EmptyFunction_ShouldReturnEmptyResponse(): void
	{
		$response = $this->whenRecordOnFunctionThat(
			$this->doNothing()
		);
		$this->thenCountOfErrorsOnResponseIs($response, 0);
		$this->thenCountOfDomainEventsIs($response, 0);
	}

	public function testRecordOn_FunctionThrowingFatalError_ShouldReturnResponseWithOneFatalError(): void
	{
		$response = $this->whenRecordOnFunctionThat(
			$this->throwFatalException()
		);

		$this->thenCountOfErrorsOnResponseIs($response, 1);
		$this->thenCountOfDomainEventsIs($response, 0);
		$this->thenFirstErrorHasMessage($response, ResponseRecorder::FATAL_ERROR_MESSAGE);
	}

	public function testRecordOn_FunctionThrowingShowableError_ShouldReturnResponseWithShowableError(): void
	{

		$testMessage = 'test_message';
		$testParams = ['foo' => 'bar'];

		$response = $this->whenRecordOnFunctionThat(
			$this->throwShowableException($testMessage, $testParams)
		);

		$this->thenCountOfErrorsOnResponseIs($response, 1);
		$this->thenCountOfDomainEventsIs($response, 0);
		$this->thenFirstErrorHasMessage($response, $testMessage);
		$this->thenFirstErrorHasMessageParams($response, $testParams);
	}

	public function testRecordOn_FunctionThrowingCommandDispatchExceptionWithPreviousShowableException_ShouldReturnResponseWithShowableError(
	): void
	{
		$testMessage = 'test_message';
		$testParams = ['foo' => 'bar'];

		$response = $this->whenRecordOnFunctionThat(
			$this->throwCommandDispatchExceptionWithPreviousShowable($testMessage, $testParams)
		);

		$this->thenCountOfErrorsOnResponseIs($response, 1);
		$this->thenCountOfDomainEventsIs($response, 0);
		$this->thenFirstErrorHasMessage($response, $testMessage);
		$this->thenFirstErrorHasMessageParams($response, $testParams);
	}

	public function testRecordOn_FunctionThrowingCommandDispatchExceptionWithPreviousFatalException_ShouldReturnResponseWithShowableFatalError(
	): void
	{
		$response = $this->whenRecordOnFunctionThat(
			$this->throwCommandDispatchExceptionWithPreviousFatal()
		);

		$this->thenCountOfErrorsOnResponseIs($response, 1);
		$this->thenCountOfDomainEventsIs($response, 0);
		$this->thenFirstErrorHasMessage($response, ResponseRecorder::FATAL_ERROR_MESSAGE);
	}

	public function testRecordOn_FunctionDispatchingErrorDomainEvent_ShouldReturnResponseWithShowableError(): void
	{
		$testPayload = ['test_payload'];
		$testErrorDomainEvent = new TestTranslatableDomainErrorDomainEvent($testPayload);

		$eventBus = $this->eventBus;
		$response = $this->whenRecordOnFunctionThat(
			$this->dispatchMessageEventOnce($testErrorDomainEvent, $eventBus)
		);

		$this->thenCountOfErrorsOnResponseIs($response, 1);
		$this->thenCountOfDomainEventsIs($response, 1);
		$this->thenFirstErrorHasMessage($response, TestTranslatableDomainErrorDomainEvent::TEST_ERROR_MESSAGE);
		$this->thenFirstErrorHasMessageParams($response, TestTranslatableDomainErrorDomainEvent::TEST_ERROR_MESSAGE_PARAMS);
	}

	public function testRecordOn_FunctionDispatchingTwoErrorDomainEventAndThrowingException_ShouldReturnResponseWith2EventsAnd3Errors(
	): void
	{
		$testPayload = ['test_payload'];
		$testErrorDomainEvent = new TestTranslatableDomainErrorDomainEvent($testPayload);

		$eventBus = $this->eventBus;
		$response = $this->whenRecordOnFunctionThat(
			$this->dispatchMessageTwoTimesAndThrowException($testErrorDomainEvent, $eventBus)
		);

		$this->thenCountOfErrorsOnResponseIs($response, 3);
		$this->thenCountOfDomainEventsIs($response, 2);
	}

	public function testRecordOn_FunctionDispatchingStringEvent_ShouldReturnEmptyResponse(): void
	{

		$testMessage = 'test_message';

		$eventBus = $this->eventBus;
		$response = $this->whenRecordOnFunctionThat(
			$this->dispatchMessageEventOnce($testMessage, $eventBus)
		);

		$this->thenCountOfErrorsOnResponseIs($response, 0);
		$this->thenCountOfDomainEventsIs($response, 0);
	}

	public function testRecordOn_FunctionDispatchingEvent_ShouldReturnResponseWithEvent(): void
	{
		$testPayload = ['test_payload'];
		$testDomainEvent = new TestDomainEvent($testPayload);

		$eventBus = $this->eventBus;
		$response = $this->whenRecordOnFunctionThat(
			$this->dispatchMessageEventOnce($testDomainEvent, $eventBus)
		);

		$this->thenCountOfErrorsOnResponseIs($response, 0);
		$this->thenCountOfDomainEventsIs($response, 1);
		$this->thenEventIsTestDomainEventWithPayoad($response, $testPayload);
	}

	private function whenRecordOnFunctionThat(Closure $doSomething): BasicMutationResponse
	{
		return $this->sut->recordOn($doSomething);
	}

	private function dispatchMessageEventOnce($domainEventMessage, EventBus $eventBus): Closure
	{
		return function () use ($domainEventMessage, $eventBus) {
			$eventBus->dispatch($domainEventMessage);
		};
	}

	private function dispatchMessageTwoTimesAndThrowException($domainEventMessage, EventBus $eventBus): Closure
	{
		return function () use ($domainEventMessage, $eventBus) {
			$eventBus->dispatch($domainEventMessage);
			$eventBus->dispatch($domainEventMessage);
			throw new Exception();
		};
	}

	private function throwFatalException(): Closure
	{
		return function () {
			throw new Exception();
		};
	}

	private function throwShowableException($domainEventMessage, array $testParams): Closure
	{
		return function () use ($domainEventMessage, $testParams) {
			throw new TestShowableTranslatableException($domainEventMessage, $testParams);
		};
	}

	private function throwCommandDispatchExceptionWithPreviousShowable($domainEventMessage, array $testParams): Closure
	{
		return function () use ($domainEventMessage, $testParams) {
			$previous = new TestShowableTranslatableException($domainEventMessage, $testParams);
			throw new CommandDispatchException('', 0, $previous);
		};
	}

	private function throwCommandDispatchExceptionWithPreviousFatal(): Closure
	{
		return function () {
			$previous = new Exception();
			throw new CommandDispatchException('', 0, $previous);
		};
	}

	private function thenCountOfErrorsOnResponseIs(BasicMutationResponse $response, int $expectedCount): void
	{
		self::assertCount($expectedCount, $response->getErrors());
	}

	protected function thenCountOfDomainEventsIs(BasicMutationResponse $response, int $expectedCount): void
	{
		self::assertCount($expectedCount, $response->getDomainEvents());
	}

	protected function thenFirstErrorHasMessage(BasicMutationResponse $response, $message): void
	{
		$error = $response->getErrors()->getAll()[0];

		self::assertInstanceOf(ShowableMutationError::class, $error);
		self::assertEquals($message, $error->getErrorMessage());
	}

	private function thenFirstErrorHasMessageParams(BasicMutationResponse $response, $params)
	{
		$error = $response->getErrors()->getAll()[0];

		self::assertInstanceOf(ShowableMutationError::class, $error);
		self::assertEquals($params, $error->getErrorMessageParams());
	}

	protected function thenEventIsTestDomainEventWithPayoad(BasicMutationResponse $response, $testPayload): void
	{
		$event = $response->getDomainEvents()->getAll()[0];

		self::assertInstanceOf(TestDomainEvent::class, $event);
		self::assertEquals($testPayload, $event->payload());
	}

	protected function doNothing(): Closure
	{
		return function () {
		};
	}
}
