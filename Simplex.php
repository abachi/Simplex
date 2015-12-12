<?php

/*
    This file content a random ideas and suggestions of the process that i should follow to release
    the `Simplex` program.

    $input = 'objective function and it's constraints';

    for example :
    max z = x1 + x2
    2x1 + 3x2 <= 50
    x1 + x2 <= 20

    Simplex::run($input);
 */
class Simplex
{
    /**
     * @const the pattern of the canonical form
     */
    const OBJECTIVE_FUNCTION_PATTERN = '/^((max)|(min))\sz\s?=(\s?(\+|-)\s?[1-9]*x[1-9]+\s?)+;$/';
    /**
    * @var String
    */
    private $input;

    /**
    * The number of the constraints, it's represent the number of lines in the `Simplexe` table
    * @var int
    */
    private $constraints_number;

    /**
    * The number of the variables, it's represent the number of columns in the `simplexe` table
    * @var int
    */
    private $variables_number;


    /**
    * Constructor
    * @param {String} $input content of the objective function and it's constraints.
    */
    public function __construct(){

    }

    /**
    * The main function of the script
    *
    * @param {String} $input content of the objective function and the constraints.
    */
    public function run()
    {
        if(!$this->isObjectiveFunction($this->input))
        {
            // throw an exception content the error line and column
        }

            // now i must to organize the work by spliting the $input to array
            $lexics = [
                'obj_fun' => 'max z = x1 + x2',
                'const_'+$this->constraints_number => ''
		];
    }

    /**
     * Check if the input given is an objective function
     *
     * @param {string} $input
     * @return boolean
     */
    public function isObjectiveFunction($input)
    {
        return preg_match(Simplex::OBJECTIVE_FUNCTION_PATTERN, $input);
    }

}

