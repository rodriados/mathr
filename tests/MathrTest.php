<?php

use Mathr\Mathr;
use Mathr\Exception\ScopeException;
use PHPUnit\Framework\TestCase;

final class MathrTest extends TestCase
{
	/**
	 * @var Mathr $mathr
	 */
	protected $mathr;
	
	public function setUp()
	{
		$this->mathr = new Mathr;
	}
	
	public function testCanBeCreated()
	{
		$this->assertInstanceOf(
			Mathr::class,
			$this->mathr
		);
	}
	
	public function testCanDeclareVariable()
	{
		$this->mathr->evaluate("a = 1024");
		$this->mathr->evaluate("b = 10");
		
		$this->assertEquals(
			$this->mathr->evaluate("a"),
			1024
		);
		
		$this->assertEquals(
			$this->mathr->evaluate("b"),
			10
		);
		
		$this->mathr->delVariable("a");
		
		$this->expectException(\Mathr\Exception\NodeException::class);
		$this->mathr->evaluate("a");
	}
	
	public function testCanDeclareFunction()
	{
		$this->mathr->evaluate("f(x) = (x + 1) ^ 2");
		
		$this->assertEquals(
			$this->mathr->evaluate("f(3)"),
			16
		);
		
		$this->mathr->delFunction("f");
		
		$this->expectException(\Mathr\Exception\NodeException::class);
		$this->mathr->evaluate("f(3)");
	}
	
	public function testCanDeclareRecursiveFunction()
	{
		$this->mathr->evaluate("fib(0) = 0");
		$this->mathr->evaluate("fib(1) = 1");
		$this->mathr->evaluate("fib(x) = fib(x - 1) + fib(x - 2)");
		
		$this->assertEquals(
			$this->mathr->evaluate("fib(10)"),
			55
		);
	}
	
	public function testLimitsRecursionDepth()
	{
		$this->testCanDeclareRecursiveFunction();
		$this->expectException(ScopeException::class);
		$this->mathr->evaluate("fib(30)");
	}
	
	public function testCanImplicitlyMultiply()
	{
		$this->assertEquals(
			$this->mathr->evaluate("3(4+2)"),
			18
		);
	}
	
	public function testKnowsOperatorPrecedence()
	{
		$this->assertEquals(
			$this->mathr->evaluate("3+4*5"),
			23
		);
	}
	
	public function testCanUseVariableAsFunctionParameter()
	{
		$this->testCanDeclareVariable();
		$this->testCanDeclareFunction();
		$this->testCanDeclareRecursiveFunction();
		
		$this->assertEquals(
			$this->mathr->evaluate("fib(b)"),
			55
		);
		
		$this->assertEquals(
			$this->mathr->evaluate("f(a)"),
			1050625
		);
	}
	
	public function testKnowsSomeMathConstants()
	{
		$this->assertEquals(
			$this->mathr->evaluate("pi"),
			M_PI
		);
		
		$this->assertEquals(
			$this->mathr->evaluate("e"),
			M_E
		);
	}
	
	public function testKnowsSomeCommomFunctions()
	{
		$this->assertEquals(
			$this->mathr->evaluate("sqrt(81)"),
			9
		);
		
		$this->assertEquals(
			$this->mathr->evaluate("log(8,2)"),
			3
		);
	}
	
	public function testCanUseVariableWithCommomFunctions()
	{
		$this->testCanDeclareVariable();
		
		$this->assertEquals(
			$this->mathr->evaluate("log(a,2)"),
			10
		);
	}
	
	public function testCanExport()
	{
		$this->mathr->evaluate("fib(0) = 1");
		$this->mathr->evaluate("fib(1) = 1");
		$this->mathr->evaluate("fib(x) = fib(x-2) + fib(x-1)");
		$this->mathr->evaluate("a = 10");
		$this->mathr->evaluate("elefante = 6");
		$this->mathr->evaluate("rtN(x, N) = x ^ (1/N)");
		
		$expect = file_get_contents(__DIR__."/scope.json");
		$this->assertEquals($expect, $this->mathr->export());
	}
	
	public function testCanImport()
	{
		$this->mathr->import(file_get_contents(__DIR__."/scope.json"));
		$this->assertEquals($this->mathr->evaluate("rtN(8,3)"), 2);
		$this->assertEquals($this->mathr->evaluate("fib(elefante)"), 13);
	}
}
