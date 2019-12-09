<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder;

use Doctrine\ORM\Query\ResultSetMapping;
use Nette\SmartObject;

final class ResultStatements {

	use SmartObject;

	/** @var string */
	private $sql;

	/** @var array */
	private $params;

	/** @var ResultSetMapping */
	private $rsm;

	public function __construct(string $sql, array $params, ResultSetMapping $rsm) {
		$this->sql = $sql;
		$this->params = $params;
		$this->rsm = $rsm;
	}

	public function getSql(): string {
		return $this->sql;
	}

	public function getParams(): array {
		return $this->params;
	}

	public function getRsm(): ResultSetMapping {
		return $this->rsm;
	}

}
