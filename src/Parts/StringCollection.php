<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parts;

use Nette\SmartObject;

class StringCollection implements IPart {

	use SmartObject;

	/** @var array */
	protected $parts = [];

	/** @var string */
	private $separator;

	public function __construct(string $separator = ', ') {
		$this->separator = $separator;
	}

	public function isValid(): bool {
		return (bool) $this->parts;
	}

	public function clean() {
		$this->parts = [];

		return $this;
	}

	public function add(string $part) {
		$this->parts[] = $part;

		return $this;
	}

	public function __toString(): string {
		return implode($this->separator, $this->parts);
	}

}
