<?php declare(strict_types = 1);

namespace TomCizek\ResponseRecorder\DomainModel;

use Exception;
use Throwable;

abstract class AbstractShowableTranslatableDomainException extends Exception implements ShowableTranslatableDomainException
{
	/** @var array|mixed[] */
	protected $messageParameters;

	/**
	 * @param string         $message
	 * @param array|mixed[]  $messageParameters
	 * @param int            $code
	 * @param Throwable|null $previous
	 */
	public function __construct(
		string $message = '',
		array $messageParameters = [],
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
		$this->messageParameters = $messageParameters;
	}

	/**
	 * @return array|mixed[]
	 */
	public function getMessageParameters(): array
	{
		return $this->messageParameters;
	}
}
