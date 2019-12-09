<?php declare(strict_types = 1);

namespace WebChemistry\DoctrineQueryBuilder\Parts;

interface IPart {

	public function isValid(): bool;

	public function __toString(): string;

}