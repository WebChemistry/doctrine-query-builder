<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parts;

use Nette\SmartObject;

class Where {

	use SmartObject;

	const AND = 'AND';
	const OR = 'OR';

	/** @var string */
	private $type;

	/** @var string */
	private $expression;

	public function __construct(string $expression, string $type = self::AND) {
		$this->type = $type;
		$this->expression = $expression;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}

	public function __toString() {
		if (strpos($this->expression, 'AND') !== false || strpos($this->expression, 'OR') !== false) {
			return '(' . $this->expression . ')';
		}
		return $this->expression;
	}

}
