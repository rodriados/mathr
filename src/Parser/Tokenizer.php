<?php
/**
 * Mathr\Parser\Tokenizer class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Parser;

use Iterator;
use Mathr\Exception\UnknownTokenException;

class Tokenizer
	implements Iterator
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
			'|([a-zA-Zα-ωΑ-Ω_][a-zA-Zα-ωΑ-Ω0-9_]*)'.    # Group 2: Variables or functions
			'|(\+|-|\/|\*)'.                            # Group 3: Right to Left Operators
			'|(\^|=)'.                                  # Group 4: Left to Right Operators
			'|(\(|\))'.                                 # Group 5: Parentheses and comma
			'|(,)'.                                     # Group 6: Comma
			'/',
			$expression,
			$this->data,
			PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE
		);
	}
	
	/**
	 * Retuns the current iteration data.
	 * @return mixed Currently iterated token.
	 * @throws UnknownTokenException
	 */
	public function current() : Token
	{
		if(($data = $this->data[1][$this->position][0]) !== "") {
			return Token::number($data);
		}
		
		if($data = $this->data[2][$this->position][0]) {
			if(
				($this->position + 1) < count($this->data[5]) &&
			    is_array($this->data[5][$this->position + 1]) &&
				$this->data[5][$this->position + 1][0] == '('
			) {
				$this->next();
				return Token::function($data);
			}
			
			return Token::variable('$'.$data);
		}
		
		if($data = $this->data[3][$this->position][0]) {
			return Token::operator($data, Token::RIGHT);
		}
		
		if($data = $this->data[4][$this->position][0]) {
			return Token::operator($data, Token::LEFT);
		}
		
		if($data = $this->data[5][$this->position][0]) {
			return Token::parentheses($data == '(');
		}
		
		if($data = $this->data[6][$this->position][0]) {
			return Token::comma();
		}
		
		throw new UnknownTokenException;
	}
	
	/**
	 * Advances the iteration position in one.
	 * @return int The new iterator position.
	 */
	public function next()
	{
		++$this->position;
		return $this->position;
	}
	
	/**
	 * Returns the current iteration key.
	 * @return int Expression iteration position.
	 */
	public function key() : int
	{
		return $this->data[0][$this->position][1];
	}
	
	/**
	 * Informs whether the iterator is still valid.
	 * @return bool Is the iterator valid?
	 */
	public function valid() : bool
	{
		return $this->position < count($this->data[0]);
	}
	
	/**
	 * Rewinds the iterator position to the first position.
	 * @return bool It always succeeds.
	 */
	public function rewind()
	{
		$this->position = 0;
		return true;
	}
	
}
