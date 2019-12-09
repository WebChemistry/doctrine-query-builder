<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Mapping;

use InvalidArgumentException;
use Nette\SmartObject;

final class EntityMapping {

	use SmartObject;

	/** @var string[] */
	private $mapping = [];

	/** @var string[] */
	private $reverse = [];

	public function add(string $entity, string $alias): void {
		$this->mapping[$alias] = $entity;
		$this->reverse[$entity] = $alias;
	}

	public function get(string $alias): string {
		if (!isset($this->mapping[$alias])) {
			throw new InvalidArgumentException("Mapping not exists for $alias");
		}

		return $this->mapping[$alias];
	}

	public function getAliasByEntity(string $entity): string {
		if (!isset($this->reverse[$entity])) {
			throw new InvalidArgumentException("Alias not exists for $entity");
		}

		return $this->reverse[$entity];
	}

}
