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
		    array(3,  4, 1, 0, 0, 0, '=', 4200),
		    array(1,  3, 0, 1, 0, 0, '=', 2250),
		    array(1,  2, 0, 0, 1, 0, '=', 2600),
		    array(2,  0, 0, 0, 0, 1, '=', 1100)
		);
		$this->assertTrue($simplex->getConstraints() == $standard);
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
		    array(3,  0, 0,'<=', 4200),
		    array(1,  3, 0,'<=', 2250),
		    array(1,  0, 0,'<=', 2600),
		    array(2,  0, 0,'<=', 1100)
		);
		$this->assertTrue($test == $result);
	}

	/**
	* @testdox it should return the table_of_calculs
	* @test
	*/
	public function it_should_return_the_table_of_calculs()
	{
		$simplex = self::getInstance('examples/example_table.txt');
		$simplex->standardize();
		$simplex->initTableOfCalculus();
		$simplex->calculus();
		$table = $simplex->getTableOfCalculus();
		$execepted = array(
			'vars_coeffs' => array(2, 4, 0, 0, 0),
			'vars_names' => array('X1', 'X2', 'X3', 'X4', 'X5'),
			'base_vars_names'  => array('X3', 'X4', 'X5'),
			'base_vars_coeffs'  => array(0, 0, 0),
			'constraints_coeffs' => array(
				array(1, 1, 1, 0, 0),
				array(2, 3, 0, 1, 0),
				array(1, 3, 0, 0, 1)
			),
			'bases'  	 => array(100, 240, 210),
			'thetas' 	 => array(100, 80, 70),
			'zj' 		 => array(0, 0, 0, 0, 0),
			'cj-zj' 		 => array(2, 4, 0, 0, 0)
		);
		$this->assertTrue($table == $execepted);
	}
}