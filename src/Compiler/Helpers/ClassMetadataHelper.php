<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Compiler\Helpers;

use Doctrine\ORM\Mapping\ClassMetadata;
use Nette\SmartObject;

final class ClassMetadataHelper {

	use SmartObject;

	public static function getColumnName(ClassMetadata $metadata, string $field): string {
		if ($metadata->hasField($field)) {
			$column = $metadata->getColumnName($field);
		} else {
			$column = $metadata->getSingleAssociationJoinColumnName($field);
		}

		return $column;
	}

}
