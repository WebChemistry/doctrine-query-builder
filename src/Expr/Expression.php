<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Expr;

use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\QueryBuilder;

class Expression {

	use SmartObject;

	/** @var QueryBuilder */
	private $queryBuilder;

	public function __construct(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
	}

	public function subQuery(QueryBuilder $builder): string {
		$this->queryBuilder->addParameters($builder->getParameters());

		return '(' . $builder->getDql() . ')';
	}

	public function parametrize(string $expression, ...$params): string {
		return sprintf(strtr($expression, [
			'%' => '%%',
			'?' => '%s',
		]), ...$params);
	}

}
