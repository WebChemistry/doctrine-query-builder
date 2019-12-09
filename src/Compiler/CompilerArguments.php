<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Compiler;

use Doctrine\ORM\Query\ResultSetMapping;
use WebChemistry\DoctrineQueryBuilder\Mapping\EntityMapping;

final class CompilerArguments {

	/** @var ResultSetMapping */
	private $rsm;

	/** @var EntityMapping */
	private $mapping;

	/** @var Aliasing */
	private $aliasing;

	public function __construct(ResultSetMapping $rsm, EntityMapping $mapping, Aliasing $aliasing) {
		$this->rsm = $rsm;
		$this->mapping = $mapping;
		$this->aliasing = $aliasing;
	}

	public function getRsm(): ResultSetMapping {
		return $this->rsm;
	}

	public function getMapping(): EntityMapping {
		return $this->mapping;
	}

	public function getAliasing(): Aliasing {
		return $this->aliasing;
	}

}
