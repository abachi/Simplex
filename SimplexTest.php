<?php

require_once 'Simplex.php';
class SimplexTest extends PHPUnit_Framework_TestCase
{

    /**
     * @testdox It should confirm that the input is matching the objective function pattern
     * @test
     */
    public function testShouldReturnCorrectMatchingOfObjectiveFunction()
    {
       $simplex = new Simplex;
        $input = 'max z =-5x1+x1 + x2 - x3 + 52x3;';
        $simplex->setInput($input);
        $this->assertEquals(true, $simplex->isObjectiveFunction($input));

        $input = 'min z =-5x1+x1 + x2 - x3 + 52';
        $simplex->setInput($input);
        $this->assertEquals(false, $simplex->isObjectiveFunction($input));

        $input = 'MaX z = -x1 + x2 - x3;';
        $simplex->setInput($input);
        $this->assertEquals(true, $simplex->isObjectiveFunction($input));
    }

    /**
     * @testdox It should confirm that the input is matching the constraints pattern
     * @test
     */
    public function testShouldReturnCorrectMatchingOfConstraints()
    {
        $simplex = new Simplex();

        $input = '-3x1 + 2x2 <= 125;';
        $simplex->setInput($input);
        $this->assertEquals(true, $simplex->isConstraint($input));

        $input = '-3x1 + 2x <= 125;';
        $simplex->setInput($input);
        $this->assertEquals(false, $simplex->isConstraint($input));

        $input = '-3x1 - 2x2 = -15;';
        $simplex->setInput($input);
        $this->assertEquals(true, $simplex->isConstraint($input));
    }
}