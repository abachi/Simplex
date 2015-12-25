<?php

/**
 * Class Simplex
 */
class Simplex
{

	/**
	* Objective function coefficients
	* @var array
	*/
	private $obj_function;

	/**
	* Constraints coefficients
	* @var array
	*/
	private $constraints;

	/**
	* Optimization action `max` OR `min`
	* @var string
	*/
	private $opt_action;

   /**
   * Table of the calculus
   * @var array
   */
   private $table;

   /**
   * Number of all the variables
   * @var integer
   */
   private $total_vars_number;

   /**
   * Initialize the object
   *
   * @param array $obj_function
   * @param array $constraints
   * @param string 'max' | 'min'
   * @return void
   */
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
   * Initialize the table of calculus
   *
   * @return void
   */
   public function initTableOfCalculus()
   {  
         $this->initVariablesCoeffs();
         $this->initVariablesNames();
         $this->initBaseVariablesNames();
         $this->initBaseVariablesCoeffs();
         $this->initConstraintsCoeffs();
         $this->initConstraintsBases();       
   }  

   /**
   * Start calculus
   *
   * @return void
   */
   public function calculus()
   {
         // start calculus
         $this->setZj($this->caclulateZj());
         $cj_zj = $this->caclulateCjMinusZj();
         $this->setCjMinusZj($cj_zj);

         //verifier the optimality 
         //$this->isOptimal();

      // variable entrante
      $varIn = $this->getVariableIn($cj_zj);
       $varOut = $this->getVariableOut($varIn);
       // var_dump($varOut);
       // var_dump($varIn);
       if($varOut === false)
            throw exception('No variable want to out');
   }   

  /**
  * Initialization of the variables coefficients
  *
  * @return void
  */
  private function initVariablesCoeffs()
  {
      $obj_func_coeffs = $this->getObjectiveFunction();
      $n   = $this->getTotalVarsNumber();
      $len = sizeof($obj_func_coeffs);

      foreach($obj_func_coeffs as $c){
         $all_coeffs[] = $c;
      }   
      for ($i=$len; $i < $n ; $i++) { 
            $all_coeffs[] = 0;
      }
       $this->setVariablesCoeffs($all_coeffs);
  }

  /**
  * Initialization of the variables names
  *
  * @return void
  */
  private function initVariablesNames()
  {
      $n   = $this->getTotalVarsNumber();
      for ($i=1; $i < $n+1; $i++) { 
         $vars_names[] = 'X'.$i; 
      }
      $this->setVariablesNames($vars_names);
  } 

  /**
  * Initialization of the base variables names
  *
  * @return void
  */
  private function initBaseVariablesNames()
  {
      $n   = $this->getTotalVarsNumber();
      $len = sizeof($this->getObjectiveFunction());
      for ($i=$len; $i < $n ; $i++) { 
            $base_vars_names [] = $this->getVariableNameByIndex($i);
      }
      $this->setBaseVariablesNames($base_vars_names);
  } 

  /**
  * Initialization of the base variables coefficients
  *
  * @return void
  */
  private function initBaseVariablesCoeffs()
  {
      $n   = $this->getTotalVarsNumber();
      $len = sizeof($this->getObjectiveFunction());
     for ($i=$len; $i < $n ; $i++) { 
           $base_vars_coeffs [] = $this->getVariableCoeffByIndex($i);
     }
     $this->setBaseVariablesCoeffs($base_vars_coeffs);
  } 

  /**
  * Initialization of constraints coefficients
  *
  * @return void
  */
  private function initConstraintsCoeffs()
  {
      $constraints = $this->getConstraints();
      foreach ($constraints as $constraint) {
            array_pop($constraint); // delete the base 
            array_pop($constraint); // delete the '=' symbol
            $coeffs[] = $constraint;
         }   
      $this->setConstraintsCoeffs($coeffs);
  } 

  /**
  * Initialization of constraints bases
  *
  * @return void
  */
  private function initConstraintsBases()
  {
       $constraints = $this->getConstraints();
       $constraints_size = sizeof($constraints);
       $constraint_size = sizeof($constraints[0]);
        for ($i=0; $i < $constraints_size ; $i++) { 
             $bases [] = $constraints[$i][$constraint_size-1];
       }
       $this->setConstraintsBases($bases);
  } 

   /**
   * Calculate theta
   *
   * @return float | string
   */
   private function CalculateTheta($b, $a)
   {
         if($a !== 0)
            return $b/$a;

         return 'infinite';
   }  

   /**
   * Determine witch variable must go out of the base
   *
   * @param array $varIn
   * @return string
   */
   public function getVariableOut($varIn)
   {
         $pos = array_search($varIn, $this->getConstraints());
         $Ai = $this->getConstraintsColumn($pos+1);
         $bases = $this->getConstraintsBases();
         $table = $this->getTableOfCalculus();
         $len = sizeof($bases);

         for ($i=0; $i < $len; $i++) { 
           $t = $this->CalculateTheta($bases[$i], $Ai[$i]);
           $thetas[] = $t;
            if($t > 0 && $t !== 'infinite'){
               $positives[] = $t;
            }
         }
         $this->setThetas($thetas);
         if(sizeof($positives) < 1)
            return fales; // to indicate no variable want to go out 

         sort($positives);
         $pos = array_search($positives[0], $thetas);
         return $table['base_vars_names'][$pos] ;
   } 

   /**
   * Check if the table is optimal
   *
   * @return boolean
   */
   public function isOptimal()
   {
      $cj_zj = $this->getCjMinusZj();
      $len = sizeof($cj_zj);
      $opt_action = $this->getOptimizationAction();

      if($opt_action === 'max'){
         for ($i=0; $i < $len; $i++) { 
           if($cj_zj[$i] > 0)
               return false;
         }
      }else{
         for ($i=0; $i < $len; $i++) { 
           if($cj_zj[$i] < 0)
               return false;
         }
      }
      return true;
   }

   /**
   * Determine witch variable must go in of the base
   *
   * @param array $cj_$zj
   * @return string
   */
   public function getVariableIn($cj_zj)
   {
      $opt_action = $this->getOptimizationAction();
      // max((cj - zj) > 0)
      if($opt_action === 'max'){
         $value = max(array_filter($cj_zj, function($value){
            return $value > 0;
         }));
      }else{ // min((cj - zj) < 0)
          $value = min(array_filter($cj_zj, function($value){
            return $value < 0;
         }));
      }
     return $this->getVariableNameByIndex(array_search($value, $cj_zj));
   }

   /**
   * Calculate Cj - Zj
   *
   * @return array
   */
   public function caclulateCjMinusZj()
   {
         $cj = $this->getVariablesCoeffs();
         $zj = $this->getZj();
         $len = sizeof($zj);

         for ($i=0; $i < $len; $i++) { 
            $cj_zj [] = $cj[$i] - $zj[$i];
         }
         return $cj_zj;
   }

   /**
   * Get an array of Cj - Zj
   *
   * @return array
   */
   public function getCjMinusZj()
   {
      return  $this->table['cj-zj'];
   }

   /**
   * Set an array of Cj - Zj
   *
   * @return void
   */
   public function setCjMinusZj($cjzj)
   {
      $this->table['cj-zj'] = $cjzj;
   }

   /**
   * Get constraints coefficients
   *
   * @return array
   */
   public function getConstraintsCoeffs()
   {
      return  $this->table['constraints_coeffs'];
   }

   /**
   * Set constraints coefficients
   *
   * @return void
   */
   public function setConstraintsCoeffs($coeffs)
   {
      $this->table['constraints_coeffs'] = $coeffs;
   }

   /**
   * Calculate Zj
   *
   * @return array
   */
   public function caclulateZj()
   {
       $cvb = $this->getBaseVariablesCoeffs();
       $constraints = $this->getConstraints();
       $consts = sizeof($cvb);
       $coeffs = sizeof($constraints[0]) - 2;

      for ($i=0; $i < $coeffs ; $i++) { 
          $sum = 0;
          for ($j=0; $j < $consts; $j++) { 
            $sum = ($constraints[$j][$i] * $cvb[$j]) + $sum;
         }
         $zj[] = $sum;
      }
       return $zj;
   }

   /**
   * Set Zj
   *
   * @return void
   */
   public function setZj($zj)
   {
      $this->table['zj'] = $zj;
   }  

   /**
   * Get Zj
   *
   * @return array
   */
   public function getZj()
   {
      return $this->table['zj'];
   }  

   /**
   * Get the constraints array
   *
   * @return array
   */
   public function getConstraints()
   {
   		return $this->constraints;
   }

   /**
   * Get a constraints column by index $j
   * 
   * @param integer $j
   * @return array
   */
   public function getConstraintsColumn($j)
   {  
         $constraints = $this->getConstraints();
         $len = sizeof($constraints);
         for ($i=0; $i < $len; $i++) { 
            $result[] = $constraints[$i][$j];
         }
         return $result;
   }

   /**
   * Set the constraints
   * 
   * @param array $constraints
   * @return void
   */
   public function setConstraints($constraints)
   {
   		$this->constraints = $constraints;
   }

   /**
   * Get the thetas
   *
   * @return array
   */
   public function getThetas()
   {
         return $this->table['thetas'];
   }

   /**
   * Set the thetas
   *
   * @param array $thetas
   * @return void
   */
   public function setThetas($thetas)
   {
         $this->table['thetas'] = $thetas;
   }

   /**
   * Get the objective function coefficients
   *
   * @return array
   */
   public function getObjectiveFunction()
   {
   		return $this->obj_function;
   }

   /**
   * Get the optimization action
   *
   * @return string
   */
   public function getOptimizationAction()
   {
   		return $this->opt_action;
   }

   /**
   * Get the table of calculus
   *
   * @return array
   */
   public function getTableOfCalculus()
   {
         return $this->table;
   }

   /**
   * Set the table of calculus
   *
   * @param array $table
   * @return void
   */
   public function setTableOfCalculus($table)
   {
         $this->table = $table;
   }

   /**
   * Get a variable name by index in the table['vars_names']
   *
   * @param integer $index
   * @return string
   */
   public function getVariableNameByIndex($index)
   {
         return $this->table['vars_names'][$index];
   }

   /**
   * Set a variable name by index in the table['vars_names']
   *
   * @param string  $name
   * @param integer $index
   * @return void
   */
   public function setVariableNameByIndex($name, $index)
   {
          $this->table['vars_names'][$i] = $name;
   }

   /**
   * Get a variable coefficient by index in the table['vars_coeffs']
   *
   * @param integer $index
   * @return integer
   */
   public function getVariableCoeffByIndex($index)
   {
         return $this->table['vars_coeffs'][$index];
   }

   /**
   * Set a variable coefficient by index in the table['vars_coeffs']
   *
   * @param integer  $coeff
   * @param integer $index
   * @return void
   */
   public function setVariableCoeffByIndex($coeff, $index)
   {
         $this->table['vars_coeffs'][$i] = $coeff;
   }

   /**
   * Get the constraint base by index
   *
   * @param integer $index
   * @return integer
   */
   public function getConstraintBaseByIndex($index)
   {
         return $this->table['bases'][$index];
   }

   /**
   * Set the constraint base by index
   *
   * @param integer $base
   * @param integer $index
   * @return void
   */
   public function setConstraintBaseByIndex($base, $index)
   {
          $this->table['bases'][$index] = $base;
   }

   /**
   * Get all the constraints bases
   *
   * @return array
   */
   public function getConstraintsBases()
   {
         return $this->table['bases'];
   }

   /**
   * Set the constraints bases
   *
   * @param array $bases
   * @return void
   */
   public function setConstraintsBases($bases)
   {
          $this->table['bases'] = $bases;
   }

   /**
   * Get the names of the variables in the base
   *
   * @return array
   */
   public function getBaseVariablesNames()
   {
         return $this->table['base_vars_names'];
   }

   /**
   * Set the names of the variables in the base
   *
   * @return void
   */
   public function setBaseVariablesNames($names)
   {
         $this->table['base_vars_names'] = $names;
   }

   /**
   * Set the name of variable in the base by index
   *
   * @param string $name
   * @param integer $index
   * @return void
   */
   public function setBaseVariableNameByIndex($name, $index)
   {
         $this->table['base_vars_names'][$index] = $name;
   }

   /**
   * Get the name of variable in the base by index
   *
   * @param integer $index
   * @return string
   */
   public function getBaseVariableNameByIndex($index)
   {
         return $this->table['base_vars_names'][$index];
   }

   /**
   * Get the coefficients of the base variables from the table
   *
   * @return array
   */
   public function getBaseVariablesCoeffs()
   {
         return $this->table['base_vars_coeffs'];
   }

   /**
   * Set the coefficients of the base variables
   *
   * @return void
   */
   public function setBaseVariablesCoeffs($coeffs)
   {
         $this->table['base_vars_coeffs'] = $coeffs;
   }

   /**
   * Set the coefficient of the base variable by index
   *
   * @param integer $coeff
   * @param integer $index
   * @return void
   */
   public function setBaseVariableCoeffByIndex($coeff, $index)
   {
         $this->table['base_vars_coeffs'][$index] = $coeff;
   }

   /**
   * Get the coefficient of the base variable by index
   *
   * @param integer $coeff
   * @param integer $index
   * @return void
   */
   public function getBaseVariableCoeffByIndex($index)
   {
         return $this->table['base_vars_coeffs'][$index];
   }

   /**
   * Get the coefficients of the base variables
   *
   * @return array
   */
   public function getVariablesCoeffs()
   {
         return $this->table['vars_coeffs'];
   }

   /**
   * Set the coefficients of the base variables
   *
   * @param array $coeffs
   * @return void
   */
   public function setVariablesCoeffs($coeffs)
   {
         $this->table['vars_coeffs'] = $coeffs;
   }

   /**
   * Get the variables names from the table
   *
   * @return array
   */
   public function getVariablesNames()
   {
         return $this->table['vars_names'];
   }

   /**
   * Set the variables names
   *
   * @param array ²$names
   * @return void
   */
   public function setVariablesNames($names)
   {
         $this->table['vars_names'] = $names;
   }

   /**
   * Set the total number of variables (objective function vars + the constraints must standardize) 
   *
   * @param integer $n
   * @return void
   */
   public function setTotalVarsNumber($n)
   {
      $this->total_vars_number = $n;
   }

   /**
   * Get the total number of variables (objective function vars + the constraints must standardize) 
   *
   * @return integer
   */
   public function getTotalVarsNumber()
   {
      return $this->total_vars_number;
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
		$this->setTotalVarsNumber(sizeof($constraints_must_standardize) + $obj_func_vars_number );
		$result = $this->replaceMissingConstraints($constraints_must_standardize, $this->getTotalVarsNumber());
		$result = $this->addPositiveQuantity($result, $obj_func_vars_number);
		$this->setConstraints($result);
   }

   /**
   * Add positive quantity to the constraints to make the constraint an equation
   *
   * @param array $constraints
   * @param integer $start, the position for starting adding the positive quantity
   * @return array
   */
   public function addPositiveQuantity($constraints, $start)
   {
   			$all_constraints = sizeof($constraints);
   			$one_constraint = sizeof($constraints[0]);
   			for ($i=0; $i < $all_constraints; $i++) { 
   				$constraints[$i][$start] = 1;
   				$constraints[$i][$one_constraint-2] = '=';
   				$start++;
   			}
   			return $constraints;
   }

   /**
   * Replace the missing variables in each constraint by 0 as a coefficient
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

