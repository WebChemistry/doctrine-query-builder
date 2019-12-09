<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Compiler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use InvalidArgumentException;
use Nette\SmartObject;
use UnexpectedValueException;
use WebChemistry\DoctrineQueryBuilder\Compiler\Helpers\ClassMetadataHelper;
use WebChemistry\DoctrineQueryBuilder\Mapping\EntityMapping;
use WebChemistry\DoctrineQueryBuilder\Parser\Parser;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Func;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Method;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\NestedSelection;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Other;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Selection;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\DQLTokenizer;

final class Compiler {

	use SmartObject;

	/** @var EntityManagerInterface */
	private $em;

	/** @var EntityMapping */
	private $mapping;

	/** @var EntityMethods */
	private $entityMethods;

	/** @var CompilerArguments */
	private $arguments;

	public function __construct(EntityManagerInterface $em, EntityMethods $entityMethods) {
		$this->em = $em;
		$this->entityMethods = $entityMethods;
	}

	protected function precompile(array $tokens) {
		$mapping = new EntityMapping();
		foreach ($tokens as $token) {
			if ($token instanceof Method) {
				$func = $token->getFunction();

				if ($func->getName() === 'as') {
					$mapping->add($func->getParameters()[0], $token->getSelection()->getBase());
				}
			}
		}

		$this->mapping = $mapping;
	}

	public function compile(string $dql): array {
		$sql = '';
		$tokens = Parser::parse(DQLTokenizer::tokenize($dql));
		$this->precompile($tokens);

		$this->arguments = new CompilerArguments(
			$rsm = new ResultSetMapping(), $this->mapping, new Aliasing($this->mapping)
		);

		foreach ($tokens as $token) {
			if ($token instanceof Other) {
				$sql .= $token->getValue();
			} else if ($token instanceof Selection) {
				$sql .= $this->selection($token);
			} else if ($token instanceof Method) {
				$sql .= $this->method($token);
			} else if ($token instanceof NestedSelection) {
				$sql .= $this->nestedSelection($token);
			} else if ($token instanceof Func) {
				$sql .= $this->func($token);
			} else {
				throw new UnexpectedValueException();
			}
		}

		return [$sql, $rsm];
	}

	private function selection(Selection $selection): string {

	}

	private function method(Method $method): string {
		$selection = $method->getSelection();
		$func = $method->getFunction();
		if ($selection->getCount() > 2) {
			throw new UnexpectedValueException('Only two-depth nested selection is supported');
		}

		$alias = $selection->getBase();
		$entity = $this->mapping->get($alias);
		$method = $func->getName();
		$params = $func->getParameters();

		return $this->entityMethods->call($this->arguments, $entity, $alias, $method, $params);
	}

	private function nestedSelection(NestedSelection $selection): string {
		if ($selection->getCount() > 2) {
			throw new UnexpectedValueException('Only two-depth nested selection is supported');
		}

		$entity = $this->arguments->getMapping()->get($selection->getBase());
		$metadata = $this->em->getClassMetadata($entity);
		$field = $selection->getRemaining();

		return $selection->getBase() . '.' . ClassMetadataHelper::getColumnName($metadata, $field);
		//return $this->arguments->getAliasing()->columnByAlias($selection->getBase(), ClassMetadataHelper::getColumnName($metadata, $field));
	}

	private function func(Func $func): string {
		$params = $func->getParameters();
		switch ($func->getName()) {
			case 'int':
				$this->arguments->getRsm()->addScalarResult($params[0], $params[0], 'integer');

				return $params[0];
			default:
				throw new InvalidArgumentException("Function {$func->getName()} not exists");
		}
	}

}
