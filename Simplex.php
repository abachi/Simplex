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
   * Set the constraints array
   *
   * @return void
   */
   public function setConstraints($constraints)
   {
   		$this->constraints = $constraints;
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
   public function getOptimizationAction()
   {
   		return $this->opt_action;
   }

   /**
   * Transform the constraints from the canonical form to standard form
   *
   * @return void
   */
   public function standardize()
   {
   		$obj_func_vars_number 		  = sizeof($this->obj_function);
		$constraints_must_standardize = array();

	   	// replace the coefficients of the missing variables with 0
		$constraints =  $this->replaceMissingConstraints($this->getConstraints(), $obj_func_vars_number);
		foreach ($constraints as $key => $constraint) {
			if(array_search('<=', $constraint) !== false){
				$constraints_must_standardize[$key] = $constraint;
			}
		}
		
		$total_vars_number = sizeof($constraints_must_standardize) + $obj_func_vars_number;
		$result = $this->replaceMissingConstraints($constraints_must_standardize, $total_vars_number);
		$result = $this->addPositiveQuantity($result, $obj_func_vars_number);
		$this->setConstraints($result);
   }

   /**
   * Add positive quantity to the constraints to make the constraint an equation
   *
   * @param array $constraints
   * @param integer $start, the offset for starting adding the positive quantity
   * @return array
   */
   public function addPositiveQuantity($constraints, $start)
   {
   			$all_constraints = sizeof($constraints);
   			$one_constraint = sizeof($constraints[0]);
   			for ($i=0; $i < $all_constraints; $i++) { 
   				$constraints[$i][$start] = '1';
   				$constraints[$i][$one_constraint-2] = '=';
   				$start++;
   			}
   			return $constraints;
   }

   /**
   * Replace the missign variables in each constraint by 0 as a coefficient
   *
   * @param array $constraints
   * @param integer $vars_number, the number of variable must be in each equation
   * @return array
   */
   public function replaceMissingConstraints($constraints, $vars_number)
   {
   		$temp = array();
   		$result = array();
   		foreach ($constraints as $key => $constraint) {
   			$temp[0] = array_pop($constraint);
   			$temp[1] = array_pop($constraint);
   			$len = sizeof($constraint);
   			if( $len <= $vars_number ){
   				for ($i=$len - 1 ; $i < $vars_number-1; $i++) { 
   					$constraint[] = '0';
   				}
   				$constraint[] = $temp[1];
   				$constraint[] = $temp[0];
   			}
   			$result[] = $constraint;
   		}
   		return $result;
   }
}

