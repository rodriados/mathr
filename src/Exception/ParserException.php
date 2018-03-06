<?php
/**
 * Mathr\Exception\ParserException class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Exception;

use Mathr\Token;

class ParserException extends MathrException
{
	/**
	 * Creates a mismatched parentheses exception.
	 * @param Token $token Token that raised the exception.
	 * @return ParserException
	 */
	public static function mismatched(Token $token): self
	{
		return new self(sprintf(
			"Mismatched '%s' in position %d.",
			$token->getData(), $token->getPosition()
		));
	}
	
	/**
	 * Creates an unexpected token exception.
	 * @param Token $token Token that raised the exception.
	 * @return ParserException
	 */
	public static function unexpected(Token $token): self
	{
		return new self(sprintf(
			"Unexpected token '%s' in position %d.",
			$token->getData(), $token->getPosition()
        ));
	}
	
	public static function unknown(): self
	{
		return new self("An unknown error occurred during parsing.");
	}
	
}
