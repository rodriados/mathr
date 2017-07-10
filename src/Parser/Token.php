<?php
/**
 * Mathr\Parser\Token class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Parser;

class Token
{
	private $data;
	private $type;
	
	const NUMBER        = 0x00000001;
	const VARIABLE      = 0x00000002;
	const OPERATOR      = 0x00000004;
	const FUNCTION      = 0x00000008;
	const PARENTHESES   = 0x00000010;
	const COMMA         = 0x00000020;
	
	const RIGHT         = 0x00010000;
	const LEFT          = 0x00020000;
	
	protected function __construct(string $data, int $type)
	{
		$this->data = $data;
		$this->type = $type;
	}
	
	public function __toString()
	{
		return $this->data;
	}
	
	public function data()
	{
		return $this->data;
	}
	
	public function is(int $check) : bool
	{
		return ($this->type & $check) == $check;
	}
	
	public static function number(string $data) : self
	{
		return new self($data, self::NUMBER);
	}
	
	public static function variable(string $data) : self
	{
		return new self($data, self::VARIABLE);
	}
	
	public static function operator(string $data, int $assoc) : self
	{
		return new self($data, self::OPERATOR | $assoc);
	}
	
	public static function function(string $data) : self
	{
		return new self(
			$data,
			self::FUNCTION | self::OPERATOR | self::PARENTHESES | self::LEFT
		);
	}
	
	public static function parentheses(bool $opener) : self
	{
		return $opener
			? new self('(', self::PARENTHESES | self::LEFT)
			: new self(')', self::PARENTHESES | self::RIGHT);
	}
	public static function comma() : self
	{
		return new self(',', self::COMMA);
	}
	
}
