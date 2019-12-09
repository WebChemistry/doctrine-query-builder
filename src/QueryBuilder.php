<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder;

use Doctrine\ORM\EntityManagerInterface;
use Nette\SmartObject;
use RuntimeException;
use WebChemistry\DoctrineQueryBuilder\Expr\Expression;
use WebChemistry\DoctrineQueryBuilder\Parts\From;
use WebChemistry\DoctrineQueryBuilder\Parts\IPart;
use WebChemistry\DoctrineQueryBuilder\Parts\JoinCollection;
use WebChemistry\DoctrineQueryBuilder\Parts\StringCollection;
use WebChemistry\DoctrineQueryBuilder\Parts\Where;
use WebChemistry\DoctrineQueryBuilder\Parts\WhereCollection;

class QueryBuilder {

	use SmartObject;

	/** @var Expression|null */
	private $expr;

	/** @var JoinCollection */
	protected $partJoin;

	/** @var StringCollection */
	protected $partGroup;

	/** @var StringCollection */
	protected $partOrder;

	/** @var From|null */
	protected $partFrom;

	/** @var WhereCollection */
	protected $partWhere;

	/** @var StringCollection */
	protected $partSelect;

	/** @var EntityManagerInterface */
	protected $em;

	/** @var int|null */
	protected $maxResults;

	/** @var int|null */
	protected $offset;

	/** @var mixed[] */
	private $parameters = [];

	/** @var IQueryFactory */
	private $queryFactory;

	public function __construct(EntityManagerInterface $em, IQueryFactory $queryFactory, string $from, string $alias) {
		$this->em = $em;
		$this->partSelect = new StringCollection();
		$this->partWhere = new WhereCollection();
		$this->partFrom = null;
		$this->partOrder = new StringCollection();
		$this->partGroup = new StringCollection();
		$this->partJoin = new JoinCollection();
		$this->queryFactory = $queryFactory;

		$this->from($from, $alias);
		$this->select('%' . $alias . '.select()');
	}

	public function expr(): Expression {
		if (!$this->expr) {
			$this->expr = new Expression($this);
		}

		return $this->expr;
	}

	public function setMaxResults(?int $maxResults) {
		$this->maxResults = $maxResults;

		return $this;
	}

	public function setOffset(?int $offset) {
		$this->offset = $offset;

		return $this;
	}

	public function setParameter($name, $value) {
		$this->parameters[$name] = $value;

		return $this;
	}

	public function setParameters(iterable $parameters) {
		$this->parameters = $parameters;

		return $this;
	}

	public function addParameters(array $parameters) {
		$this->parameters = array_merge($this->parameters, $parameters);

		return $this;
	}

	public function getParameters(): array {
		return $this->parameters;
	}

	public function select($select) {
		$this->partSelect->clean();
		$this->addSelect($select);

		return $this;
	}

	public function addSelect($select) {
		$this->partSelect->add($select);

		return $this;
	}

	public function from(string $from, string $alias) {
		$this->partFrom = new From('%' . $alias . '.as(' . $from . ')');

		return $this;
	}

	public function leftJoin(string $entity, string $column, string $alias) {
		$this->partJoin->add('LEFT', $entity, $column, $alias);

		return $this;
	}

	public function orderBy(string $column, string $type = 'ASC') {
		$this->partOrder->clean();
		$this->addOrderBy($column, $type);

		return $this;
	}

	public function addOrderBy(string $column, string $type = 'ASC') {
		$this->partOrder->add($column . ' ' . $type);

		return $this;
	}

	public function groupBy(string $expression) {
		$this->partGroup->clean();
		$this->addGroupBy($expression);

		return $this;
	}

	public function addGroupBy(string $expression) {
		$this->partGroup->add($expression);

		return $this;
	}

	public function where($expression) {
		$this->partWhere->clean();
		$this->andWhere($expression);

		return $this;
	}

	public function andWhere($expression) {
		$this->partWhere->add(new Where($expression, 'AND'));

		return $this;
	}

	public function orWhere($expression) {
		$this->partWhere->add(new Where($expression, 'OR'));

		return $this;
	}

	protected function buildPart(IPart $part, ?string $stmt): string {
		if (!$part->isValid()) {
			return '';
		}

		return ($stmt !== null ? $stmt . ' ' : '') . (string) $part . ' ';
	}

	public function getQuery(): Query {
		$query = $this->queryFactory->create($this->selfToString());
		$query->setParameters($this->parameters);

		return $query;
	}

	public function __toString(): string {
		return $this->selfToString();
	}

	public function getDql(): string {
		return $this->selfToString();
	}

	protected function selfToString(): string {
		$sql = $this->buildPart($this->partSelect, 'SELECT');
		if (!$this->partFrom) {
			throw new RuntimeException('part FROM must be set');
		}
		$sql .= $this->buildPart($this->partFrom, 'FROM');
		$sql .= $this->buildPart($this->partJoin, null);
		$sql .= $this->buildPart($this->partWhere, 'WHERE');
		$sql .= $this->buildPart($this->partGroup, 'GROUP BY');
		$sql .= $this->buildPart($this->partOrder, 'ORDER BY');

		if ($this->maxResults) {
			$sql .= 'LIMIT ' . $this->maxResults . ' ';
		}
		if ($this->offset) {
			$sql .= 'OFFSET ' . $this->offset . ' ';
		}

		return substr($sql, 0, -1);
	}

}
