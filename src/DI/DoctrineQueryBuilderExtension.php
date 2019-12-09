<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\DI;

use Nette\DI\CompilerExtension;
use WebChemistry\DoctrineQueryBuilder\Bridges\Filter\DoctrineQueryBuilderDataSourceFactory;
use WebChemistry\DoctrineQueryBuilder\Compiler\Compiler;
use WebChemistry\DoctrineQueryBuilder\Compiler\EntityMethods;
use WebChemistry\DoctrineQueryBuilder\IQueryBuilderFactory;
use WebChemistry\DoctrineQueryBuilder\IQueryFactory;
use WebChemistry\DoctrineQueryBuilder\QueryBuilder;
use WebChemistry\Filter\DataSource\DataSourceRegistry;

final class DoctrineQueryBuilderExtension extends CompilerExtension {

	public function loadConfiguration() {
		$builder = $this->getContainerBuilder();

		$builder->addFactoryDefinition($this->prefix('queryBuilderFactory'))
			->setImplement(IQueryBuilderFactory::class);

		$builder->addFactoryDefinition($this->prefix('queryFactory'))
			->setImplement(IQueryFactory::class);

		$builder->addDefinition($this->prefix('compiler'))
			->setType(Compiler::class);

		$builder->addDefinition($this->prefix('entityMethods'))
			->setType(EntityMethods::class);

		$builder->addDefinition($this->prefix('dataSourceFactory'))
			->setType(DoctrineQueryBuilderDataSourceFactory::class);
	}

	public function beforeCompile() {
		$builder = $this->getContainerBuilder();

		$name = $builder->getByType(DataSourceRegistry::class);

		if ($name) {
			$builder->getDefinition($name)
				->addSetup('addFactory', [QueryBuilder::class, $this->prefix('@dataSourceFactory')]);
		}
	}

}
