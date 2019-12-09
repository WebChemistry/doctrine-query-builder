<?php

use WebChemistry\DoctrineQueryBuilder\Tokenizer\DQLTokenizer;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\Token;

class TokenizerTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
	}

	protected function _after() {
	}

	// tests
	public function testSelection() {
		$this->assertEquals([
			new Token('%entity', 0, Token::TOKEN_SELECTION)
		], DQLTokenizer::tokenize('%entity'));
		$this->assertEquals([
			new Token('%entity.field', 0, Token::TOKEN_NESTED_SELECTION)
		], DQLTokenizer::tokenize('%entity.field'));
	}

	public function testFunction() {
		$this->assertEquals([
			new Token('%func()', 0, Token::TOKEN_FUNCTION),
		], DQLTokenizer::tokenize('%func()'));
	}

	public function testMethod() {
		$this->assertEquals([
			new Token('%entity.func()', 0, Token::TOKEN_METHOD),
		], DQLTokenizer::tokenize('%entity.func()'));
	}

	public function testPos() {
		$this->assertEquals([
			new Token(' ', 0, Token::TOKEN_OTHER),
			new Token('%entity.func()', 1, Token::TOKEN_METHOD),
		], DQLTokenizer::tokenize(' %entity.func()'));
	}

	public function testTwoTokens() {
	}

}