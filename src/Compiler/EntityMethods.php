<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Compiler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Utility\PersisterHelper;
use InvalidArgumentException;
use Nette\SmartObject;
use WebChemistry\DoctrineQueryBuilder\Compiler\Helpers\ClassMetadataHelper;

final class EntityMethods {

	private const METHODS = [
		'select' => true,
		'as' => true,
		'full' => true,
		'discriminator' => true,
	];

	use SmartObject;

	/** @var EntityManagerInterface */
	private $em;

	/** @var ClassMetadata */
	private $metadata;

	/** @var string */
	private $alias;

	/** @var CompilerArguments */
	private $arguments;

	public function __construct(EntityManagerInterface $em) {
		$this->em = $em;
	}

	public function call(CompilerArguments $arguments, string $entity, string $alias, string $method, array $params): string {
		$this->arguments = $arguments;
		$this->metadata = $this->em->getClassMetadata($entity);
		$this->alias = $alias;

		if (!isset(self::METHODS[$method])) {
			throw new InvalidArgumentException("Method $method not exists");
		}

		return call_user_func_array([$this, $method], $params);
 	}

	protected function discriminator(...$entities): string {
		$values = [];
		$column = '';
		foreach ($entities as $entity) {
			$meta = $this->em->getClassMetadata($entity);
			$values[] = $meta->discriminatorValue;
			$column = $meta->discriminatorColumn['name'];
		}

		return $this->alias . '.' . $column . ' IN(' . implode(',', $values) . ')';
 	}

	protected function as(): string {
		$this->arguments->getRsm()->addEntityResult($this->metadata->getName(), $this->alias);

		return $this->metadata->getTableName() . ' AS ' . $this->alias;
 	}

	protected function full(string $field): string {
		return ClassMetadataHelper::getColumnName($this->metadata, $field);
 	}

 	protected function select(...$params): string {
		if (!$params) {
			$fields = null;
		} else {
			$fields = $params;
		}

		$aliasing = $this->arguments->getAliasing();
		$rsm = $this->arguments->getRsm();
		$rootEntity = $this->metadata->getName();

		$classes = $this->metadata->subClasses;
		array_unshift($classes, $this->metadata->getName());

		$columns = [];
		$processed = [];
		foreach ($classes as $class) {
			$metadata = $this->em->getClassMetadata($class);
			$entity = $metadata->getName();

			// fields
			foreach ($metadata->getFieldNames() as $field) {
				if (isset($processed[$field])) {
					continue;
				}
				$processed[$field] = true;

				if ($fields !== null && !isset($fields[$field])) {
					continue;
				}

				$columns[] = $aliasing->select($rootEntity, $columnName = $metadata->getColumnName($field));

				$rsm->addFieldResult($this->alias, $aliasing->alias($rootEntity, $columnName), $field, $entity);
			}

			// associations
			foreach ($metadata->associationMappings as $mapping) {
				$field = $mapping['fieldName'];
				if (isset($processed[$field])) {
					continue;
				}
				$processed[$field] = true;

				if ($fields !== null && !isset($fields[$field])) {
					continue;
				}
				if (!$mapping['isOwningSide']) {
					continue;
				}
				if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY) {
					continue;
				}
				$type = PersisterHelper::getTypeOfColumn($mapping['joinColumns'][0]['referencedColumnName'], $this->metadata, $this->em);

				$columns[] = $aliasing->select($rootEntity, $columnName = $metadata->getSingleAssociationJoinColumnName($field));
				$rsm->addMetaResult($this->alias, $aliasing->alias($rootEntity, $columnName), $columnName, false, $type);
			}
		}

		// discriminator
		if ($mapping = $this->metadata->discriminatorColumn) {
			$columns[] = $aliasing->select($rootEntity, $mapping['name']);
			$alias = $aliasing->alias($rootEntity, $mapping['name']);

			$rsm->setDiscriminatorColumn($this->alias, $alias);
			$rsm->addMetaResult($this->alias, $alias, $mapping['fieldName'], false, $mapping['type']);
		}

		return implode(', ', $columns);
	}

}
