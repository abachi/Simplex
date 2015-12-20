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
    	$pattren = '/(\s+|\t+)/';
        return strtolower(preg_replace($pattren, ' ', trim($input)));
    }
    /**
     * Get the objective function from a text text given
     *
     * @param {string} $text
     * @return string
     */
    public static function getObjectiveFunction($text)
    {
       
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



}