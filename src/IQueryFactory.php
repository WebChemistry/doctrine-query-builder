<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder;

interface IQueryFactory {

	public function create(string $dql): Query;

}
