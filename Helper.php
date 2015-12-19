<?php
/**
*
*/
class Helper {

	const EQUATION_FIRST_PART = '//'; 

	/**
	* Check if the given string is matchthe equation form
	*
	* @param string 
	* @return boolean
	*/
	public static function isEquation($equation)
	{
		return preg_match(EQUATION_FIRST_PART, Helper::stringReset($equation));
	}

	/**
  	* Get the of numbers (coefficients) of an equation given
  	* 	
  	* @param string $equation i.e `5x1 + 2x2`
  	* @return array 
	*/
	public static function getCoefficients($equation)
	{
		$equation = Helper::stringReset($equation);

		return preg_match('//', $equation);

	}

	/**
     * Reset the input given by removing the spaces and tabs
     *
     * @param {string} $input
     * @return string
     */
    public static function stringReset($input)
    {
        return strtolower(preg_replace('/\s+/', '', trim($input)));
    }

}