<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder;

use Doctrine\ORM\Query\ResultSetMapping;

final class Statements {

	/** @var string */
	private $sql;

	/** @var ResultSetMapping */
	private $rsm;

	/** @var array */
	private $parameters;

	public function __construct(string $sql, ResultSetMapping $rsm, array $parameters) {
		$this->sql = $sql;
		$this->rsm = $rsm;
		$this->parameters = $parameters;
	}

	public function getSql(): string {
		return $this->sql;
	}

	public function getRsm(): ResultSetMapping {
		return $this->rsm;
	}

	public function getParameters(): array {
		return $this->parameters;
	}

}
