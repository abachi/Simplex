<?php

require_once './Helper.php';
require_once './Simplex.php';
class SimplexTest extends PHPUnit_Framework_TestCase
{

	/**
	* Return an instance of the Simplex class
	*
	* @param string $path of text file
	* @return Simplex
	*/
	protected static function getInstance($path)
	{
		$text = Helper::getContent($path);
		$obj_function = Helper::getObjectiveFunction($text);
		$constraints = Helper::getConstraintsMatrix($text);
		$opt_action = Helper::getOptimisationAction($text);
		return new Simplex($obj_function, $constraints, $opt_action);

	}

	/**
	* @testdox it should return standardization of the canonical form
	* @test
	*/
	public function it_should_check_if_the_constraints_are_standardized()
	{
		$simplex = self::getInstance('examples/example.txt');
		$this->assertNotTrue($simplex->isStandardized());

		$simplex = self::getInstance('examples/example_standard_form.txt');
		$this->assertTrue($simplex->isStandardized());
	}

	/**
	* @testdox it should return standard form of the constraints
	* @test
	*/
	public function it_should_return_standard_form_of_the_constraints()
	{
		$simplex = self::getInstance('examples/example.txt');
		$this->assertNotTrue($simplex->isStandardized());
		$simplex->standardize();
		$this->assertTrue($simplex->isStandardized());
		
		$standard = array(
		    array('1', '-2', '1', '=', '50'),
		    array('3',  '5', '1', '=', '10'),
		    array('2',  '2', '1', '=', '100')
		);
		$this->assertTrue($simplex->getConstraints() === $standard);
	}
}