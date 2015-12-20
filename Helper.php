<?php
/**
*
*/
class Helper {

    /**
     * Get the content of file text
     *
     * @param {string} $path
     * @return string
     */
    public static function getContent($path)
    {
        if(!is_file($path))
            throw new Exception('Invalid path');

        $result = file_get_contents($path);
        if(is_string($result) === false)
        {
             throw new Exception('Invalid content');        
        }
        return Helper::stringReset($result);
    }

	/**
     * Reset the input given by removing the spaces and tabs
     *
     * @param {string} $input
     * @return string
     */
    public static function stringReset($input)
    {
        $pattren = "/\r\n/";
        $result = preg_replace($pattren, ';', $input);
        $pattren = "/(\s+|\t+)/";
        $result = preg_replace($pattren, ' ', trim($result));
        return strtolower($result);
    }

    /**
     * Get the objective function as string
     *
     * @param {string} $text
     * @return string
     */
    public static function getObjectiveFunction($text)
    {
       $text = Helper::stringReset($text); // lines chars are replaced by `;`
       $lines = explode(';', $text);
       return $lines[0];
    }

    /**
     * Get the coefficients of an objective function
     *
     * @param {string} $text
     * @return array
     */
    public static function getObjectiveFunctionCoeffs($text)
    {
       $obj_function = Helper::getObjectiveFunction($text);
        $str_coeffs = explode(' ', preg_replace('/(min|max)/', '', $obj_function));
        $coeffs = array();
        foreach($str_coeffs as $key => $value){
            $coeff = is_numeric(trim($value));
            if(!$coeff){
                continue;
            }else{
                $coeffs[] = floatval($value);
            }
        }
        return $coeffs; 
    }

    /**
     * Get the optimisation action of an objective function given
     *
     * @param {string} $obj_function
     * @return string
     */
    public static function getOptimisationAction($obj_function)
    {
        preg_match('/(max|min)/', Helper::stringReset($obj_function), $opt);
        return $opt[0];
    }
     /**
     * Remove the elements that not useful (like value of an element is "\n")
     *
     * @param Array $array
     * @return Array
     */
    public static function arrayCleaner($array)
    {
        $special_chars = array("/\n/", "/\r/", "/\t/", "/\r\n/");
        $clean = array();
        foreach ($array as $key => $value) {
            $str = preg_replace($special_chars, '', $value);
            if(strlen($str) > 0 && $str !== ' '){
                $clean[] = $str;
            }
        }
        return $clean;
    }

    /**
     * Get the constraints matrix (coefficients of the constraints)
     *
     * @param {string} $text
     * @return array
     */
    public static function getConstraintsMatrix($text)
    {
        $constraints = explode(';', $text);
        $constraints = array_splice($constraints, 1); // delete the objective function
        $constraints = Helper::arrayCleaner($constraints);
        $result = array();
        foreach ($constraints as $key => $constraint) {
            $temp_arr = explode(' ', $constraint);
            $result [] = Helper::arrayCleaner($temp_arr);
        }
       return $result;
    }


}