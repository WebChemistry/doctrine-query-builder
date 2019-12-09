<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parser\Types;

use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\Token;

final class Method {

	use SmartObject;

	/** @var string */
	private $value;

	/** @var Func */
	private $function;

	/** @var NestedSelection */
	private $selection;

	/** @var Token */
	private $token;

	public function __construct(string $value, Token $token) {
		$base = strrpos($value, '.') + 1;
		$this->function = new Func(substr($value, $base), $token);
		$this->selection = new NestedSelection(substr($value, 0, strpos($value, '(')), $token);
		$this->token = $token;
	}

	public function getToken(): Token {
		return $this->token;
	}

	public function getFunction(): Func {
		return $this->function;
	}

	public function getSelection(): NestedSelection {
		return $this->selection;
	}

}
