<?php

/**
 * Class Simplex
 */
class Simplex
{

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

	public function __construct($obj_function, $constraints, $opt_action)
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

   /**
   * Return the constraints array
   *
   * @return array
   */
   public function getConstraints()
   {
   		return $this->constraints;
   }

   /**
   * Return the objective function coefficients
   *
   * @return array
   */
   public function getObjectiveFunction()
   {
   		return $this->obj_function;
   }
   /**
   * Return the optimization action
   *
   * @return string
   */
   public function getOptimisationAction()
   {
   		return $this->opt_action;
   }
}

