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
		$obj_function = Helper::getObjectiveFunctionCoeffs($text);
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
		$simplex = self::getInstance('examples/real_standard_example.txt');
		$this->assertNotTrue($simplex->isStandardized());
		$simplex->standardize();
		$this->assertTrue($simplex->isStandardized());
		$standard = array(
		    array('3',  '4', '1', '0', '0', '0', '=', '4200'),
		    array('1',  '3', '0', '1', '0', '0', '=', '2250'),
		    array('1',  '2', '0', '0', '1', '0', '=', '2600'),
		    array('2',  '0', '0', '0', '0', '1', '=', '1100')
		);
		$this->assertTrue($simplex->getConstraints() === $standard);
	}
	/**
	* @testdox it should replace the missign variables by zero as a coefficient
	* @test
	*/
	public function it_should_replace_the_missign_variables_by_zero_as_a_coefficient()
	{
		$simplex = self::getInstance('examples/example_complete_constraints.txt');
		$result = $simplex->replaceMissingConstraints($simplex->getConstraints(), 3);
		$test =  array(
		    array('3',  '0', '0','<=', '4200'),
		    array('1',  '3', '0','<=', '2250'),
		    array('1',  '0', '0','<=', '2600'),
		    array('2',  '0', '0','<=', '1100')
		);
		$this->assertTrue($test === $result);
	}
}