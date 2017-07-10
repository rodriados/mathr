<?php
/**
 * Mathr\Parser\Tokenizer class file.
 * @package Parser
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
	private $position = 0;
	private $data;
	
	public function __construct(string $expression)
	{
		preg_match_all(
			'/([0-9]*\.[0-9]+|[0-9]+\.?)'.  # Group 1: Number literals
			'|([a-zA-Z_][a-zA-Z0-9_]*)'.    # Group 2: Variables or functions
			'|(\+|-|\/|\*)'.                # Group 3: Right to Left Operators
			'|(\^|=)'.                      # Group 4: Left to Right Operators
			'|(\(|\))'.                     # Group 5: Parentheses and comma
			'|(,)'.                         # Group 6: Comma
			'/',
			$expression,
			$this->data,
			PREG_PATTERN_ORDER|PREG_OFFSET_CAPTURE
		);
	}
	
	/**
	 * @throws UnknownTokenException
	 * @return mixed
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
	 * @return mixed
	 */
	public function next()
	{
		++$this->position;
		return $this->position;
	}
	
	/**
	 * @return mixed
	 */
	public function key() : int
	{
		return $this->data[0][$this->position][1];
	}
	
	/**
	 * @return mixed
	 */
	public function valid() : bool
	{
		return $this->position < count($this->data[0]);
	}
	
	/**
	 * @return mixed
	 */
	public function rewind()
	{
		$this->position = 0;
		return true;
	}
	
}
