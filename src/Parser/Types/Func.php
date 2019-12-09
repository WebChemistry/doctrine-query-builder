<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parser\Types;

use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\Token;

final class Func {

	use SmartObject;

	/** @var string */
	private $name;

	/** @var int */
	private $pos;

	/** @var string[] */
	private $parameters = [];

	/** @var Token */
	private $token;

	public function __construct(string $value, Token $token) {
		$del = strpos($value, '(');

		$this->name = substr($value, 0, $del);
		$this->parseParameters(substr($value, $del + 1, -1));
		$this->token = $token;
	}

	/**
	 * @return string[]
	 */
	public function getParameters(): array {
		return $this->parameters;
	}

	public function getName(): string {
		return $this->name;
	}

	private function parseParameters(string $expr): void {
		$this->parameters = array_filter(array_map('trim', explode(',', $expr)));
	}

}
