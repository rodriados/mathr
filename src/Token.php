<?php
/**
 * Mathr\Token class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

class Token
{
	/**#@+
	 * Token type constants. These constants are responsible for informing the
	 * kind of token the instance holds.
	 */
	const NUMBER        = 0x0001;
	const VARIABLE      = 0x0002;
	const OPERATOR      = 0x0004;
	const FUNCTION      = 0x0008;
	const PARENTHESES   = 0x0010;
	const COMMA         = 0x0020;
	const UNKNOWN       = 0x0F00;
	/**#@-*/
	
	/**#@+
	 * Token associativity constants. These constants allow informing what is
	 * the current token's associativity, if any.
	 */
	const RIGHT         = 0x1000;
	const LEFT          = 0x2000;
	/**#@-*/
	
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
	 * Informs the position of the token on the string.
	 * @var int The token's position.
	 */
	private $position;
	
	/**
	 * Token constructor.
	 * @param string $data Data held by token.
	 * @param int $type Data type held by token.
	 * @param int $position The token's position.
	 */
	protected function __construct(string $data, int $type, int $position)
	{
		$this->data = $data;
		$this->type = $type;
		$this->position = $position;
	}
	
	/**
	 * Returs token as a string representation.
	 * @return string Token string representation.
	 */
	public function __toString(): string
	{
		return $this->data();
	}
	
	/**
	 * Returns token data.
	 * @return string Token data.
	 */
	public function data(): string
	{
		return $this->data;
	}
	
	/**
	 * Returns the token's position.
	 * @return int The token's position on the string.
	 */
	public function position(): int
	{
		return $this->position;
	}
	
	/**
	 * Checks whether the token type against the given value.
	 * @param int $check Type to be checked.
	 * @return bool Is token type matched?
	 */
	public function is(int $check): bool
	{
		return ($this->type & $check) == $check;
	}
	
	/**
	 * Creates a token as a number type.
	 * @param string $data Data to be held by token.
	 * @param int $position The token's position.
	 * @return Token Created token.
	 */
	public static function number(string $data, int $position): self
	{
		return new self($data, self::NUMBER, $position);
	}
	
	/**
	 * Creates a token as a variable type.
	 * @param string $data Data to be held by token.
	 * @param int $position The token's position.
	 * @return Token Created token.
	 */
	public static function variable(string $data, int $position): self
	{
		return new self($data, self::VARIABLE, $position);
	}
	
	/**
	 * Creates a token as an operator type.
	 * @param string $data Data to be held by token.
	 * @param int $assoc Informs the operator associativity.
	 * @param int $position The token's position.
	 * @return Token Created token.
	 */
	public static function operator(string $data, int $assoc, int $position): self
	{
		return new self($data, self::OPERATOR | $assoc, $position);
	}
	
	/**
	 * Creates a token as a function type.
	 * @param string $data Data to be held by token.
	 * @param int $position The token's position.
	 * @return Token Created token.
	 */
	public static function function(string $data, int $position): self
	{
		return new self(
			$data,
			self::FUNCTION | self::OPERATOR | self::PARENTHESES | self::LEFT,
			$position
		);
	}
	
	/**
	 * Creates a token as a parentheses type.
	 * @param bool $opener Is the token a left parentheses?
	 * @param int $position The token's position.
	 * @return Token Created token.
	 */
	public static function paren(bool $opener, int $position): self
	{
		return $opener
			? new self('(', self::PARENTHESES | self::LEFT, $position)
			: new self(')', self::PARENTHESES | self::RIGHT, $position);
	}
	
	/**
	 * Creates a token as a comma type.
	 * @param int $position The token's position.
	 * @return Token Created token.
	 */
	public static function comma(int $position): self
	{
		return new self(',', self::COMMA, $position);
	}
	
	/**
	 * Creates a token as an unknown type.
	 * @param int $position The token's position.
	 * @return Token Created unknown token.
	 */
	public static function unknown(int $position): self
	{
		return new self(',', self::UNKNOWN, $position);
	}
	
}
