<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Tokenizer;

final class Token {

	public const TOKEN_METHOD = 1;
	public const TOKEN_FUNCTION = 2;
	public const TOKEN_SELECTION = 3;
	public const TOKEN_NESTED_SELECTION = 4;
	public const TOKEN_OTHER = 5;

	/** @var string */
	private $value;

	/** @var int */
	private $type;

	/** @var int */
	private $pos;

	public function __construct(string $value, int $pos, int $type) {
		$this->value = $value;
		$this->pos = $pos;
		$this->type = $type;
	}

	public static function resolveType(bool $nested, bool $function): int {
		if (!$nested) {
			return $function ? self::TOKEN_FUNCTION : self::TOKEN_SELECTION;
		}

		return $function ? self::TOKEN_METHOD : self::TOKEN_NESTED_SELECTION;
	}

	public function getPos(): int {
		return $this->pos;
	}

	public function getType(): int {
		return $this->type;
	}

	public function getLength(): int {
		return strlen($this->value);
	}

	public function getValue(): string {
		return $this->value;
	}

}
