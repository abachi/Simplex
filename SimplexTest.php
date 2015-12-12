<?php

require_once 'Simplex.php';
class SimplexTest extends PHPUnit_Framework_TestCase
{

    /**
     * @testdox It_should_return_the_correct_result_of_matching
     * @test It_should_return_the_correct_result_of_matching
     */
    public function testShouldReturnCorrectMatching()
    {
       $simplex = new Simplex();
        $input = 'max z =-5x1+x1 + x2 - x3 + 52x3;';
        $this->assertEquals(true, $simplex->isObjectiveFunction($input));
        $input = 'max z =-5x1+x1 + x2 - x3 + 52';
        $this->assertEquals(false, $simplex->isObjectiveFunction($input));
        $input = 'max z = -x1 + x2 - x3;';
        $this->assertEquals(true, $simplex->isObjectiveFunction($input));
    }
}