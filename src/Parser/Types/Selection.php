<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parser\Types;

use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\Token;

final class Selection {

	use SmartObject;

	/** @var string */
	private $value;

	/** @var Token */
	private $token;

	public function __construct(string $value, Token $token) {
		$this->value = $value;
		$this->token = $token;
	}

	public function getToken(): Token {
		return $this->token;
	}

	public function getValue(): string {
		return $this->value;
	}

}
