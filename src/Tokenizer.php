<?php
/**
 * Mathr\Tokenizer class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

class Tokenizer implements \Iterator
{
	/**
	 * Expression tokens in raw data format.
	 * @var array Raw tokenizer data.
	 */
	private $data;
	
	/**
	 * Current iterator position.
	 * @var int Position being currently iterated.
	 */
	private $position = 0;
	
	/**
	 * Tokenizer constructor.
	 * @param string $expression Expression to be tokenized.
	 */
	public function __construct(string $expression)
	{
		preg_match_all(
			'/([0-9]*\.[0-9]+|[0-9]+\.?)'.              # Group 1: Number literals
			'|([A-Za-zα-ωΑ-Ω][A-Za-z0-9α-ωΑ-Ω_]*)'.     # Group 2: Variables or functions
			'|(\+|-|\/|\*)'.                            # Group 3: Right to Left Operators
			'|(\^|=)'.                                  # Group 4: Left to Right Operators
			'|(\(|\))'.                                 # Group 5: Parentheses
			'|(,)'.                                     # Group 6: Comma
			'|([^\s])'.                                 # Group 7: Unknown
			'/u',
			$expression,
			$this->data,
			PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE
		);
	}
	
	/**
	 * Retuns the current iteration data.
	 * @return Token Currently iterated token.
	 */
	public function current(): Token
	{
		$position = $this->position;
		list($data, $char) = $this->data[0][$this->position];
		
		if($this->data[1][$position][0] !== '')
			return Token::number($data, $char);
		
		if($this->data[2][$position][0]) {
			if(($position + 1) < count($this->data[5]) &&
			   is_array($this->data[5][$position + 1]) &&
			   $this->data[5][$position + 1][0] == '('
			) {
				$this->next();
				return Token::function($data, $char);
			}
			
			return Token::variable('$'.$data, $char);
		}
		
		if($this->data[3][$position][0])
			return Token::operator($data, Token::RIGHT, $char);
		
		if($this->data[4][$position][0])
			return Token::operator($data, Token::LEFT, $char);
		
		if($this->data[5][$position][0])
			return Token::paren($data == '(', $char);
		
		if($this->data[6][$position][0])
			return Token::comma($char);
		
		return Token::unknown($char);
	}
	
	/**
	 * Advances the iteration position in one.
	 * @return int The new iterator position.
	 */
	public function next(): int
	{
		return ++$this->position;
	}
	
	/**
	 * Returns the current iteration key.
	 * @return int Expression iteration position.
	 */
	public function key(): int
	{
		return $this->data[0][$this->position][1];
	}
	
	/**
	 * Informs whether the iterator is still valid.
	 * @return bool Is the iterator valid?
	 */
	public function valid(): bool
	{
		return $this->position < count($this->data[0]);
	}
	
	/**
	 * Rewinds the iterator position to the first position.
	 * @return bool It always succeeds.
	 */
	public function rewind(): bool
	{
		$this->position = 0;
		return true;
	}
	
}
