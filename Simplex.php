<?php

/**
 * Class Simplex
 */
class Simplex
{
    /**
     * @const the pattern of the constraint
     */
    const CONSTRAINT_PATTERN = '/^(((\+|-)?\s?[1-9]*x[1-9]+)\s?)+(<=|=|>=)\s?-?[0-9]+\s?;$/';
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
     *
     * @param string, optional $input content of the objective function and it's constraints.
     */
    public function __construct($input = null){
        if($input)
            $this->input = $this->stringReset($input);
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
    public function isObjectiveFunction()
    {
        return preg_match(Simplex::OBJECTIVE_FUNCTION_PATTERN, $this->input);
    }

    /**
     * Check if the input given is a constraint
     *
     * @param {string} $input
     * @return boolean
     */
    public function isConstraint()
    {
        return preg_match(Simplex::CONSTRAINT_PATTERN, $this->input);
    }
    /**
     * Reset the input given by removing the spaces and tabs
     *
     * @param {string} $input
     * @return string
     */
    public function stringReset($input)
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($input)));
    }

    /**
     * Set the input
     *
     * @param $input
     */
    public function setInput($input)
    {
        $this->input = $this->stringReset($input);
    }
}

