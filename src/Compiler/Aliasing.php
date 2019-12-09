<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Compiler;

use InvalidArgumentException;
use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\Mapping\EntityMapping;

class Aliasing {

	use SmartObject;

	/** @var EntityMapping */
	private $entityMapping;

	/** @var mixed[] */
	private $registry = [];

	public function __construct(EntityMapping $entityMapping) {
		$this->entityMapping = $entityMapping;
	}

	public function columnByAlias(string $alias, string $column): string {
		return $this->column($this->entityMapping->get($alias), $column);
	}

	public function column(string $entity, string $column): string {
		if (isset($this->registry[$entity][$column])) {
			return $this->registry[$entity][$column];
		}

		$alias = $this->entityMapping->getAliasByEntity($entity);

		return $alias . '.' . $column;
	}

	public function select(string $entity, string $column): string {
		$alias = $this->entityMapping->getAliasByEntity($entity);
		$this->registry[$entity][$column] = $select = $alias . '_' . $column;

		return $alias . '.' . $column . ' AS ' . $select;
	}

	public function alias(string $entity, string $column): string {
		if (!isset($this->registry[$entity][$column])) {
			throw new InvalidArgumentException("Alias for $entity::$column not exists");
		}

		return $this->registry[$entity][$column];
	}

}
