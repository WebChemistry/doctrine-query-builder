<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NativeQuery;
use WebChemistry\DoctrineQueryBuilder\Compiler\Compiler;

final class Query {

	/** @var string */
	private $dql;

	/** @var mixed[] */
	private $parameters;

	/** @var EntityManagerInterface */
	private $em;

	/** @var Compiler */
	private $compiler;

	/** @var Statements|null */
	private $statements;

	public function __construct(string $dql, EntityManagerInterface $em, Compiler $compiler) {
		$this->dql = $dql;
		$this->em = $em;
		$this->compiler = $compiler;
	}

	public function setParameters(array $parameters): void {
		$this->parameters = $parameters;
	}

	public function getStatements(): Statements {
		if (!$this->statements) {
			[$sql, $rsm] = $this->compiler->compile($this->dql);

			$this->statements = new Statements($sql, $rsm, $this->parameters);
		}

		return $this->statements;
	}

	public function getResult() {
		return $this->createNativeQuery()->getResult();
	}

	public function getScalarResult() {
		return $this->createNativeQuery()->getScalarResult();
	}

	/**
	 * @return mixed[]
	 */
	public function getParameters(): array {
		return $this->parameters;
	}

	public function createNativeQuery(): NativeQuery {
		$statements = $this->getStatements();

		return $this->em->createNativeQuery($statements->getSql(), $statements->getRsm())
			->setParameters($statements->getParameters());
	}

}
