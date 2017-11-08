<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\Application;

use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Prooph\ServiceBus\MessageBus;
use Psr\Log\LoggerInterface;
use Throwable;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\BasicMutationResponse;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\BasicMutationResponseBuilder;
use TomCizek\ResponseRecorder\Application\ResponseRecorder\MutationResponse\ErrorCollection\GenericShowableMutationError;
use TomCizek\ResponseRecorder\DomainModel\ShowableTranslatableDomainException;

final class ResponseRecorder
{
	public const FATAL_ERROR_MESSAGE = 'fatal_error_occured_logged';

	/** @var EventBus */
	private $eventBus;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(EventBus $eventBus, LoggerInterface $logger)
	{
		$this->eventBus = $eventBus;
		$this->logger = $logger;
	}

	public function recordOn(callable $callback): BasicMutationResponse
	{
		$responseBuilder = BasicMutationResponseBuilder::create();

		$this->attachRecordingCallbackToEventbus($responseBuilder);

		try {
			$callback();
		} catch (CommandDispatchException $exception) {
			$this->handleCommandDispatchException($exception, $responseBuilder);
		} catch (ShowableTranslatableDomainException $exception) {
			$this->handleShowableException($exception, $responseBuilder);
		} catch (Throwable $exception) {
			$this->handleFatalException($exception, $responseBuilder);
		}

		return $responseBuilder->build();
	}

	private function attachRecordingCallbackToEventbus(
		BasicMutationResponseBuilder $responseBuilder
	): void {
		$this->eventBus->attach(
			EventBus::EVENT_DISPATCH,
			$this->getRecordingCallback($responseBuilder),
			-1000
		);
	}

	private function getRecordingCallback(BasicMutationResponseBuilder $responseBuilder): \Closure
	{
		return function (ActionEvent $actionEvent) use ($responseBuilder): void {
			$event = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);
			if (!$event instanceof DomainEvent) {
				return;
			}
			$responseBuilder->addDomainEvent($event);
		};
	}

	private function handleCommandDispatchException(
		CommandDispatchException $exception,
		BasicMutationResponseBuilder $responseBuilder
	): void {
		$previous = $exception->getPrevious();
		if ($previous instanceof ShowableTranslatableDomainException) {
			$this->handleShowableException($previous, $responseBuilder);
		} else {
			$this->handleFatalException($exception, $responseBuilder);
		}
	}

	protected function handleShowableException(
		ShowableTranslatableDomainException $exception,
		BasicMutationResponseBuilder $responseBuilder
	): void {
		$responseBuilder->addError(
			GenericShowableMutationError::create(
				$exception->getMessage(),
				$exception->getMessageParameters()
			)
		);
	}

	protected function handleFatalException(
		Throwable $exception,
		BasicMutationResponseBuilder $responseBuilder
	): void {
		$this->logFatalError($exception);

		$responseBuilder->addError(
			GenericShowableMutationError::create(
				self::FATAL_ERROR_MESSAGE
			)
		);
	}

	private function logFatalError(
		Throwable $exception
	): void {
		$this->logger->error($exception);
	}
}
