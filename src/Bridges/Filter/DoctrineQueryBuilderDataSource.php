<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Bridges\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use WebChemistry\DoctrineQueryBuilder\QueryBuilder;
use WebChemistry\Filter\DataSource\IDataSource;

final class DoctrineQueryBuilderDataSource implements IDataSource {

	/** @var QueryBuilder */
	private $queryBuilder;

	/** @var array */
	private $options;

	/** @var EntityManagerInterface */
	private $em;

	public function __construct(QueryBuilder $queryBuilder, EntityManagerInterface $em, array $options) {
		$this->queryBuilder = $queryBuilder;
		$this->em = $em;
		$this->options = $options;
	}

	public function getData(?int $limit, ?int $offset): iterable {
		return $this->queryBuilder->setMaxResults($limit)->setOffset($offset)->getQuery()->getResult();
	}

	public function getItemCount(): int {
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('cnt', 'cnt', 'integer');
		$query = $this->queryBuilder->getQuery();
		$sql = 'SELECT COUNT(*) AS cnt FROM (' . $query->getStatements()->getSql() . ') xxx';

		$result = $this->em->createNativeQuery($sql, $rsm)->setParameters($query->getParameters())->getSingleScalarResult();

		return (int) $result;
	}

}
