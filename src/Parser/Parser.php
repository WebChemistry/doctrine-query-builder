<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parser;

use WebChemistry\DoctrineQueryBuilder\Parser\Types\Func;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Method;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\NestedSelection;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Other;
use WebChemistry\DoctrineQueryBuilder\Parser\Types\Selection;
use WebChemistry\DoctrineQueryBuilder\Tokenizer\Token;

final class Parser {

	/**
	 * @param object[] $tokens
	 */
	public static function parse(array $tokens): array {
		$result = [];
		foreach ($tokens as $token) {
			$value = substr($token->getValue(), 1);
			switch ($token->getType()) {
				case Token::TOKEN_SELECTION:
					$result[] = new Selection($value, $token);
					break;
				case Token::TOKEN_NESTED_SELECTION:
					$result[] = new NestedSelection($value, $token);
					break;
				case Token::TOKEN_METHOD:
					$result[] = new Method($value, $token);
					break;
				case Token::TOKEN_FUNCTION:
					$result[] = new Func($value, $token);
					break;
				case Token::TOKEN_OTHER:
					$result[] = new Other($token->getValue());
					break;
			}
		}

		return $result;
	}

}
