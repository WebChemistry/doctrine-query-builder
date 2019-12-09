<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parts;

class Select implements IPart {

	/** @var string */
	private $select;

	public function __construct(string $select) {
		$this->select = $select;
	}

	public function isValid(): bool {
		return true;
	}

	public function __toString(): string {
		return $this->select;
	}

}
