<?php
/**
 * Mathr\Parser class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Expression\Node;

class Scope
{
	protected $parent;
	protected $data;
	
	public function __construct(Scope $parent = null)
	{
		$this->parent = $parent;
		$this->data = [];
	}
	
	public function has(string $name)
	{
		return isset($this->data[$name]) or
			!is_null($this->parent) and $this->parent->has($name);
	}
	
	public function retrieve(string $name)
	{
		return isset($this->data[$name])
			? $this->data[$name]
			: (!is_null($this->parent)
				? $this->parent->retrieve($name)
				: null);
	}
	
	public function assign(string $name, array $node)
	{
		$this->data[$name] = $node;
	}
	
}
