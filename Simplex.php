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
   * coefficients of the base variables
   * @var array
   */
   private $base_vars_coeffs;

   /**
   * The names of the variables in the base
   * @var array
   */
   private $base_variables_names;

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
   * initialize the table of calculus (the first iteration of the calculus)
   *
   * @return void
   */
   public function initTable()
   {  
         // set all coeffs
         $obj_func_coeffs = $this->getObjectiveFunction();
         foreach($obj_func_coeffs as $c){
            $all_coeffs[] = $c;
         }   
         $n = $this->getTotalVarsNumber();
         for ($i=sizeof($obj_func_coeffs); $i < $n ; $i++) { 
               $all_coeffs[] = 0;
         }
          $this->setVariablesCoeffs($all_coeffs);

         // set names of vars
         for ($i=1; $i < $n+1; $i++) { 
            $vars_names[] = 'X'.$i; 
         }
         $this->setVariablesNames($vars_names);
          
          // initialization of base variables
         for ($i=sizeof($obj_func_coeffs); $i < $n ; $i++) { 
               $base_vars_names [] = $this->getVariableNameByIndex($i);
         }
         $this->setBaseVariablesNames($base_vars_names);

          // initialization of base coefficients
       for ($i=sizeof($obj_func_coeffs); $i < $n ; $i++) { 
             $base_vars_coeffs [] = $this->getVariableCoeffByIndex($i);
       }
       $this->setBaseVariablesCoeffs($base_vars_coeffs);

          // initialization of constraints bases
       $constraints = $this->getConstraints();
       $constraints_size = sizeof($constraints);
       $constraint_size = sizeof($constraints[0]);
        for ($i=0; $i < $constraints_size ; $i++) { 
             $bases [] = $constraints[$i][$constraint_size-1];
       }
       $this->setConstraintsBases($bases);

         // calculation of zj 
         $this->setZj($this->caclulateZj());
         // calculation of cj - zj 
         $cj_zj = $this->caclulateCjMinusZj();
         $this->setCjMinusZj($cj_zj);

         // verifier the optimality 
         var_dump($this->isOptimal());

      // variable entrante
      $varIn = $this->getVariableIn($cj_zj);
       $varOut = $this->getVariableOut($varIn);
       var_dump($varOut);
       var_dump($varIn);
       if($varOut === false)
            throw exception('No variable want to out');

         
   }  
   
   /**
   * Calculate theta
   *
   * @return float | string 'infinite'
   */
   private function CalculateTheta($b, $a)
   {
         if($a !== 0)
            return $b/$a;

         return 'infinite';
   }   
   /**
   * determine witch variable must go out of the base
   *
   * @param array $varIn
   * @return string
   */
   public function getVariableOut($varIn)
   {
         $pos = array_search($varIn, $this->getConstraints());
         $Ai = $this->getConstraintsColumns($pos+1);
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
         // var_dump($positives);
         $this->setThetas($thetas);
         if(sizeof($positives) < 1)
            return fales; // to indicate no variable want to out 

         sort($positives);
         var_dump($positives);
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
   * determine witch variable must go in of the base
   *
   * @param array $cj-$zj
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
   * Return an array of Cj - Zj
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
   * @return array
   */
   public function setCjMinusZj($cjzj)
   {
      $this->table['cj-zj'] = $cjzj;
   }

   /**
   * Return an array of Zj
   *
   * @return array
   */
   public function caclulateZj()
   {
       $cvb = $this->getBaseVariablesCoeffs();
       $constraints = $this->getConstraints();
       $consts = sizeof($cvb);
       $coeffs = sizeof($constraints[0]) - 2; // 1 2 3 4 = 10 

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
   * @return array of strings
   */
   public function getConstraints()
   {
   		return $this->constraints;
   }

   /**
   * Set the constraints
   * 
   * @param array of strings $constraints
   * @return void
   */
   public function getConstraintsColumns($j)
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
   * getConstraintColumns
   * @param array of strings $constraints
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
   * Set the thetas array
   *
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
   * Get the objective function coefficients
   *
   * @return array
   */
   public function getVariableNameByIndex($index)
   {
         return $this->table['vars_names'][$index];
   }
   /**
   * Get the objective function coefficients
   *
   * @return array
   */
   public function getVariableCoeffByIndex($index)
   {
         return $this->table['vars_coeffs'][$index];
   }
   /**
   * Get the objective function coefficients
   *
   * @return array
   */
   public function setVariableNameByIndex($name, $index)
   {
          $this->table['vars_names'][$i] = $name;
   }
   /**
   * set the objective function coefficients
   *
   * @return array
   */
   public function setVariableCoeffByIndex($coeff, $index)
   {
         $this->table['vars_coeffs'][$i] = $coeff;
   }

   /**
   * Get the constraint base
   *
   * @param integer $index
   * @return integer
   */
   public function getConstraintBase($index)
   {
         return $this->table['bases'][$index];
   }

   /**
   * Set the constraint base
   *
   * @return void
   */
   public function setConstraintBase($base, $index)
   {
          $this->table['bases'][$index] = $base;
   }

   /**
   * Get the constraint base
   *
   * @param integer $index
   * @return integer
   */
   public function getConstraintsBases()
   {
         return $this->table['bases'];
   }

   /**
   * Set the constraints bases
   *
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
   * Get the coefficients of the base variables from the table
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

