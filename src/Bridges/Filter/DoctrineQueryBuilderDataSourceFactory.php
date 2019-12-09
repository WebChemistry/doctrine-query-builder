<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Bridges\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Nette\SmartObject;
use WebChemistry\Filter\DataSource\IDataSource;
use WebChemistry\Filter\DataSource\IDataSourceFactory;

final class DoctrineQueryBuilderDataSourceFactory implements IDataSourceFactory {

	use SmartObject;

	/** @var EntityManagerInterface */
	private $em;

	public function __construct(EntityManagerInterface $em) {
		$this->em = $em;
	}

	public function create($source, array $options): IDataSource {
		return new DoctrineQueryBuilderDataSource($source, $this->em, $options);
	}

}
