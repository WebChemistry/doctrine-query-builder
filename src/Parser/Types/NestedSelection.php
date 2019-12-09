<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parser\Types;

use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\Token;

final class NestedSelection {

	use SmartObject;

	/** @var string[] */
	private $paths = [];

	/** @var Token */
	private $token;

	public function __construct(string $value, Token $token) {
		$this->paths = explode('.', $value);
		$this->token = $token;
	}

	public function getBase(): string {
		return $this->paths[0];
	}

	public function getRemaining(): string {
		return implode('.', array_slice($this->paths, 1));
	}

	public function getCount(): int {
		return count($this->paths);
	}

	public function getValue(): string {
		return implode('.', $this->paths);
	}

	/**
	 * @return string[]
	 */
	public function getPaths(): array {
		return $this->paths;
	}

	public function getToken(): Token {
		return $this->token;
	}

}
