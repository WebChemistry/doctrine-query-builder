<?php

use WebChemistry\DoctrineQueryBuilder\Parser\Parser;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Func;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Method;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\NestedSelection;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Selection;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\DQLTokenizer;

class ParserTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	public function testSelection() {
		$selection = Parser::parse(DQLTokenizer::tokenize('%entity'))[0];

		$this->assertInstanceOf(Selection::class, $selection);
		$this->assertSame('entity', $selection->getValue());
	}

	public function testNestedSelection() {
		$nested = Parser::parse(DQLTokenizer::tokenize('%entity.field'))[0];

		$this->assertInstanceOf(NestedSelection::class, $nested);
		$this->assertSame('entity', $nested->getBase());
		$this->assertSame(['entity', 'field'], $nested->getPaths());
	}

	public function testFunc() {
		$func = Parser::parse(DQLTokenizer::tokenize('%func()'))[0];

		$this->assertInstanceOf(Func::class, $func);
		$this->assertSame('func', $func->getName());
		$this->assertSame([], $func->getParameters());
	}

	public function testFuncParams() {
		$func = Parser::parse(DQLTokenizer::tokenize('%func(foo,bar)'))[0];

		$this->assertInstanceOf(Func::class, $func);
		$this->assertSame('func', $func->getName());
		$this->assertSame(['foo', 'bar'], $func->getParameters());
	}

	public function testMethod() {
		$method = Parser::parse(DQLTokenizer::tokenize('%entity.select()'))[0];

		$this->assertInstanceOf(Method::class, $method);
		$this->assertSame('entity.select', $method->getSelection()->getValue());
		$this->assertSame('entity', $method->getSelection()->getBase());

		$this->assertSame('select', $method->getFunction()->getName());
		$this->assertSame([], $method->getFunction()->getParameters());
	}

}