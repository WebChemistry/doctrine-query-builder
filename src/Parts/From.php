<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parts;

use Nette\SmartObject;

class From implements IPart {

	use SmartObject;

	/** @var string */
	private $expression;

	public function __construct(string $expression) {
		$this->expression = $expression;
	}

	public function isValid(): bool {
		return true;
	}

	public function __toString(): string {
		return $this->expression;
	}

}
