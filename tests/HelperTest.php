<?php

require_once './Helper.php';

class HelperTest extends PHPUnit_Framework_TestCase
{
	
	/**
	*
	* @testdox it should return the content of a text file
	* @test
	*/
	public function it_should_return_the_content_of_a_text_file()
	{
		$file = 'examples/example_text_file.txt';
		$file_content = Helper::getContent($file);
		$this->assertNotEquals(0, strlen($file_content));
		$this->assertEquals('min 1 -2 2', $file_content);
	}

	/**
	*
	* @testdox it should throw an invalid path exception
	* @expectedException Exception
	* @test
	*/
	public function it_should_throw_an_invalid_path_exception()
	{
		$file = 'example2.txt';
		$file_content = Helper::getContent($file);
	}
	
	/**
	* @testdox it should reset the text 
	* @test
	*/
	public function it_should_reset_the_text()
	{
		$text = '	MiN 		1 	-2 		2
	1 -2 <= 50
	3 5 <= 10
	2 2 <= 100';
		$new_text = Helper::stringReset($text);
		// not equals because we trim the string from the whitespaces and tabs 
		$this->assertNotEquals(strlen($text), strlen($new_text));
		$this->assertEquals('min 1 -2 2; 1 -2 <= 50; 3 5 <= 10; 2 2 <= 100', $new_text);
	}
	
	/**
	* @testdox it should return the objective function 
	* @test
	*/
	public function it_should_return_the_objective_function()
	{
		$file = 'examples/example.txt';
		$text = Helper::getContent($file);
		$obj_function = Helper::getObjectiveFunction($text);
		$this->assertEquals('min 1 -2 2 -4', $obj_function);

	}
	/**
	* @testdox it should return an array of objective function coefficients 
	* @test
	*/
	public function it_should_return_the_objective_function_coeffs()
	{
		$file = 'examples/example.txt';
		$text = Helper::getContent($file);
		$obj_function = Helper::getObjectiveFunction($text);
		$coeffs =  Helper::getObjectiveFunctionCoeffs($obj_function);
		$this->assertNotEmpty($coeffs);
		$this->assertCount(4, $coeffs);
		// TODO : change this hard way of comparing between 2 arrays please, Thank you!
		$this->assertEquals(1, $coeffs[0]);
		$this->assertEquals(-2, $coeffs[1]);
		$this->assertEquals(2, $coeffs[2]);
		$this->assertEquals(-4, $coeffs[3]);
	}

	/**
	* @testdox it should return the optimisation action 
	* @test
	*/
	public function it_should_return_the_optimisation_action()
	{
		$obj_function = 'min 1 -1 2';
		$opt = Helper::getOptimisationAction($obj_function);
		$this->assertEquals('min', $opt);

		$obj_function = 'mAx 1 -1 2';
		$opt = Helper::getOptimisationAction($obj_function);
		$this->assertEquals('max', $opt);
	}

	/**
	* @testdox it should return the constraints matrix
	* @test
	*/
	public function it_should_return_the_constraints_matrix()
	{
		$file = 'examples/example.txt';
		$text = Helper::getContent($file);
		$constraints_matrix = Helper::getConstraintsMatrix($text);
		$this->assertCount(3, $constraints_matrix);
		$this->assertTrue(is_array($constraints_matrix[0]));
		$this->assertEquals(1, $constraints_matrix[0][0]);
		$this->assertEquals(-2, $constraints_matrix[0][1]);
		// print_r($constraints_matrix);

	}
	/**
	* @testdox it should return a clean array
	* @test
	*/
	public function it_should_return_a_clean_array()
	{
		$file = 'examples/example.txt';
		$text = Helper::getContent($file);
		$array = explode(';', $text);
		$clean_arr = Helper::arrayCleaner($array);
		$this->assertNotEquals(sizeof($array), sizeof($clean_arr));
	}

}