<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder;

interface IQueryBuilderFactory {

	public function create(string $from, string $alias): QueryBuilder;

}
