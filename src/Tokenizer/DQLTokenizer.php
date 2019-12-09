<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Tokenizer;

use Nette\StaticClass;

final class DQLTokenizer {

	use StaticClass;

	/**
	 * @param string $expr
	 * @return Token[]
	 */
	public static function tokenize(string $expr): array {
		$length = strlen($expr);
		$pos = 0;

		$tokens = [];

		/** @var string $token */
		$token = null;
		$tokenPos = 0;
		$capture = false;
		$nested = false;
		$bracketRef = 0;

		while ($pos < $length) {
			$char = $expr[$pos];

			if (!$capture) {
				if ($char === '%') {
					if ($token !== null) {
						$tokens[] = new Token($token, $tokenPos, Token::TOKEN_OTHER);
					}

					$token = '%';
					$capture = true;
					$tokenPos = $pos;
				} else {
					$token .= $char;
				}
			} else {
				$token .= $char;

				// inside function
				if ($bracketRef > 0) {
					if ($char === ')') {
						$bracketRef--;
						if ($bracketRef === 0) {
							$tokens[] = new Token($token, $tokenPos, Token::resolveType($nested, true));
							$token = null;
							$capture = $nested = false;
						}
					}
				} else {
					if (ctype_alpha($char)) {
						//

					} else if ($char === '.') {
						$nested = true;

					} else if ($char === '(') {
						$bracketRef++;

					} else {
						$tokens[] = new Token(substr($token, 0, -1), $tokenPos, Token::resolveType($nested, false));

						$token = $char;
						$capture = $nested = false;
					}
				}
			}

			$pos++;
		}

		if ($capture) {
			if ($bracketRef > 0) {
				throw new TokenizerException('Unexpected end of string, missing closing bracket');
			} else {
				$tokens[] = new Token($token, $tokenPos, Token::resolveType($nested, false));
			}
		} else if ($token !== null) {
			$tokens[] = new Token($token, $tokenPos, Token::TOKEN_OTHER);
		}

		return $tokens;
	}

}
