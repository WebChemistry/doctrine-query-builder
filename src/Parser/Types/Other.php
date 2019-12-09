<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parser\Types;

use Nette\SmartObject;

final class Other {

	use SmartObject;

	/** @var string */
	private $value;

	public function __construct(string $value) {
		$this->value = $value;
	}

	public function getValue(): string {
		return $this->value;
	}

}
