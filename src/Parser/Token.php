<?php
/**
 * Mathr\Parser\Token class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Parser;

class Token
{
	const NUMBER        = 0x00000001;
	const VARIABLE      = 0x00000002;
	const OPERATOR      = 0x00000004;
	const FUNCTION      = 0x00000008;
	const PARENTHESES   = 0x00000010;
	const COMMA         = 0x00000020;
	
	const RIGHT         = 0x00010000;
	const LEFT          = 0x00020000;
	
	/**
	 * Expression data held by token.
	 * @var string Token value as string.
	 */
	private $data;
	
	/**
	 * Type of data held by token.
	 * @var int Data type held by token.
	 */
	private $type;
	
	/**
	 * Token constructor.
	 * @param string $data Data held by token.
	 * @param int $type Data type held by token.
	 */
	protected function __construct(string $data, int $type)
	{
		$this->data = $data;
		$this->type = $type;
	}
	
	/**
	 * Returs token as a string representation.
	 * @return string Token string representation.
	 */
	public function __toString()
	{
		return $this->data;
	}
	
	/**
	 * Returns token data.
	 * @return string Token data.
	 */
	public function data()
	{
		return $this->data;
	}
	
	/**
	 * Checks whether the token type against the given value.
	 * @param int $check Type to be checked.
	 * @return bool Is token type matched?
	 */
	public function is(int $check) : bool
	{
		return ($this->type & $check) == $check;
	}
	
	/**
	 * Creates a token as a number type.
	 * @param string $data Data to be held by token.
	 * @return Token Created token.
	 */
	public static function number(string $data) : self
	{
		return new self($data, self::NUMBER);
	}
	
	/**
	 * Creates a token as a variable type.
	 * @param string $data Data to be held by token.
	 * @return Token Created token.
	 */
	public static function variable(string $data) : self
	{
		return new self($data, self::VARIABLE);
	}
	
	/**
	 * Creates a token as an operator type.
	 * @param string $data Data to be held by token.
	 * @param int $assoc Informs the operator associativity.
	 * @return Token Created token.
	 */
	public static function operator(string $data, int $assoc) : self
	{
		return new self($data, self::OPERATOR | $assoc);
	}
	
	/**
	 * Creates a token as a function type.
	 * @param string $data Data to be held by token.
	 * @return Token Created token.
	 */
	public static function function(string $data) : self
	{
		return new self(
			$data,
			self::FUNCTION | self::OPERATOR | self::PARENTHESES | self::LEFT
		);
	}
	
	/**
	 * Creates a token as a parentheses type.
	 * @param bool $opener Is the token a left parentheses?
	 * @return Token Created token.
	 */
	public static function parentheses(bool $opener) : self
	{
		return $opener
			? new self('(', self::PARENTHESES | self::LEFT)
			: new self(')', self::PARENTHESES | self::RIGHT);
	}
	
	/**
	 * Creates a token as a comma type.
	 * @return Token Created token.
	 */
	public static function comma() : self
	{
		return new self(',', self::COMMA);
	}
	
}
