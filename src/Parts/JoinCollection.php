<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parts;

class JoinCollection implements IPart {

	/** @var array */
	protected $parts = [];

	public function isValid(): bool {
		return (bool) $this->parts;
	}

	public function clean(): void {
		$this->parts = [];
	}

	public function add(string $type, string $entity, string $column, string $alias) {
		$this->parts[$alias] = [$type, $entity, $column];

		return $this;
	}

	public function __toString(): string {
		$dql = '';
		foreach ($this->parts as $alias => [$type, $entity, $column]) {
			$dql .= strtoupper($type) . ' JOIN ' . "$entity.$column $alias";
		}

		return substr($dql, 0, -1);
	}

}
