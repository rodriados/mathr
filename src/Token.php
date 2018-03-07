<?php
/**
 * Mathr\Token class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
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
	public function __construct(string $data, int $type, int $position)
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
		return $this->getData();
	}
	
	/**
	 * Returns token data.
	 * @return string The token's data as a string.
	 */
	public function getData(): string
	{
		return $this->data;
	}
	
	/**
	 * Returns the token's position.
	 * @return int The token's position on the string.
	 */
	public function getPosition(): int
	{
		return $this->position;
	}
	
	/**
	 * Returns the token's type.
	 * @return int The token's type.
	 */
	public function getType(): int
	{
		return $this->type;
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
	 * @param int $pos The token's position.
	 * @return Token Created token.
	 */
	public static function number(string $data, int $pos): self
	{
		return new self($data, self::NUMBER, $pos);
	}
	
	/**
	 * Creates a token as a variable type.
	 * @param string $data Data to be held by token.
	 * @param int $pos The token's position.
	 * @return Token Created token.
	 */
	public static function variable(string $data, int $pos): self
	{
		return new self($data, self::VARIABLE, $pos);
	}
	
	/**
	 * Creates a token as an operator type.
	 * @param string $data Data to be held by token.
	 * @param int $assoc Informs the operator associativity.
	 * @param int $pos The token's position.
	 * @return Token Created token.
	 */
	public static function operator(string $data, int $assoc, int $pos): self
	{
		return new self($data, self::OPERATOR | $assoc, $pos);
	}
	
	/**
	 * Creates a token as a function type.
	 * @param string $data Data to be held by token.
	 * @param int $pos The token's position.
	 * @return Token Created token.
	 */
	public static function function(string $data, int $pos): self
	{
		return new self(
			$data,
			self::FUNCTION | self::OPERATOR | self::PARENTHESES | self::LEFT,
			$pos
		);
	}
	
	/**
	 * Creates a token as a parentheses type.
	 * @param bool $opener Is the token a left parentheses?
	 * @param int $pos The token's position.
	 * @return Token Created token.
	 */
	public static function paren(bool $opener, int $pos): self
	{
		return $opener
			? new self('(', self::PARENTHESES | self::LEFT, $pos)
			: new self(')', self::PARENTHESES | self::RIGHT, $pos);
	}
	
	/**
	 * Creates a token as a comma type.
	 * @param int $pos The token's position.
	 * @return Token Created token.
	 */
	public static function comma(int $pos): self
	{
		return new self(',', self::COMMA, $pos);
	}
	
	/**
	 * Creates a token as an unknown type.
	 * @param int $pos The token's position.
	 * @return Token Created unknown token.
	 */
	public static function unknown(int $pos): self
	{
		return new self(',', self::UNKNOWN, $pos);
	}
	
}
