<?php

/**
 * Class Simplex
 */
class Simplex
{

	/**
	* pattern to check if the constrants are sdandarized
	* @constant 
	*/
	 const STANDARD_PATTREN = '//';

	/**
	* objective function coefficients
	* @var array
	*/
	private $obj_function;
	/**
	* constraints coefficients
	* @var array
	*/
	private $constraints;
	/**
	* optimization action `MAXIMISATION` OR `MINIMISATION`
	* @var string
	*/
	private $opt_action;
	/**
	* standars form
	* @var array
	*/
	private $standard_form;

	public function __construct($obj_function, $constraints, $opt_action = 'max')
	{
		$this->obj_function = $obj_function;	
		$this->constraints = $constraints;	
		$this->opt_action = $opt_action;	
	}
   
   /**
   * Check if the constraints are standardized
   *
   * @return boolean
   */
   public function isStandardized()
   {	
   		foreach( $this->constraints as $constraint){
   			$equal_pos = sizeof($constraint) - 2;
  			if($constraint[$equal_pos] !== '=')
  					return false;
   		}
   		return true;
   }

}

