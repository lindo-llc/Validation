<?php

/**
  * Validate 
  *
  * Validation class with localisation possibility
  *
  * @author Dwayne Walsh
  * @copyright (c) 2018, Dwayne Walsh
  * @link https://github.com/DwayneWalsh
  * @since 2019.01.02
  */
     
class Validate {

	protected 	$numMin = 0,
				      $numMax = 0,
				      $lengthMin = 0,
				      $lengthMax = 0;

	public $patterns = array(
       'words'         => '[\p{L}\s]+',
       'tel'           => '[0-9+\s()-]+',
       'filename'      => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
       'folder'        => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
       'address'       => '[\p{L}0-9\s.,()°-]+'
	);

	public $responses = array(
		'empty'			=> "{{field}} must not be empty",
		'badFormat'		=> "{{field}} is invalid",
		'length'		=> "{{field}} must be between {{lenMin}} and {{lenMax}} characters long",
		'minMax'		=> "{{field}} must be between {{numMin}} and {{numMax}}",
		'int'			=> "{{field}} must be an integer",
		'float'			=> "{{field}} must be a float",
		'alpha'			=> "{{field}} must only contain letters (a-z)",
		'alphanum'		=> "{{field}} must only contain letters (a-z) and numbers (0-9)",
		'whiteSpace'	=> "{{field}} cannot contain spaces",
		'url'			=> "{{field}} must be an URL",
		'uri'			=> "{{field}} must be an URI",
		'bool'			=> "{{field}} must be a boolean (true-false)",
		'email'			=> "{{field}} must be a valid email"
	);


	public function __construct($ruleSet = null)
	{
		$this->setNewRuleSet($ruleSet);
	}

	protected $errors = array();

	private $parsed = false;


	/** Magic methods */
	public function __get($var)
	{
		return $this->{$var};
	}

	public function __set($name, $var) 
	{
		$this->{$name} = $var;
	}


	/** 
	 * Set field name
	 *
	 * @param string 	$name
	 * @return this
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/** 
	 * Value of the field
	 *
	 * @param mixed 	$value
	 * @return this
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/** 
	 * Not Empty
	 *
	 * @return this
	 */
	public function notEmpty()
	{
		if(empty($this->value)) {
			$this->errors[$this->name][] = $this->responses['empty'];
		}
		return $this;
	}

	/** 
	 * No white space
	 *
	 * @return this
	 */
	public function noWhiteSpace()
	{
		if(preg_match('/\s/',$this->value)) {
			$this->errors[$this->name][] = $this->responses['whiteSpace'];
		}
		return $this;
	}


	/** 
	 * Length
	 * For string lenth
	 *
	 * @param number 	$min
	 * @param number 	$max
	 * @return this
	 */
	public function length($min, $max)
	{
		$this->lengthMin = $min;
		$this->lengthMax = $max;

		if(strlen($this->value) < $this->lengthMin || strlen($this->value) > $this->lengthMax) {
			$this->errors[$this->name][] = $this->responses['length'];
		}

		return $this;
	}

	/** 
	 * Min Max
	 * For numeric values
	 *
	 * @param number 	$min
	 * @param number 	$max
	 * @return this
	 */
	public function minMax($min, $max)
	{
		$this->numMin = $min;
		$this->numMax = $max;

		if($this->value < $this->numMin || $this->value > $this->numMax) {
			$this->errors[$this->name][] = $this->responses['minMax'];
		}

		return $this;
	}

	public function int()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_INT)) {
			$this->errors[$this->name][] = $this->responses['int'];
		}
		return $this;
	}

	public function float()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_FLOAT)) {
			$this->errors[$this->name][] = $this->responses['float'];
		}

		return $this;
	}

	public function alpha()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/")))) {
			$this->errors[$this->name][] = $this->responses['alpha'];
		}

		return $this;
	}

	public function alphanum()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) {
			$this->errors[$this->name][] = $this->responses['alphanum'];
		}

		return $this;
	}

	public function url()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) {
			$this->errors[$this->name][] = $this->responses['url'];
		}
		return $this;
	}

	public function uri()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) {
			$this->errors[$this->name][] = $this->responses['uri'];
		}
		return $this;
	}

	public function bool()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_BOOLEAN)) {
			$this->errors[$this->name][] = $this->responses['uri'];
		}
		return $this;
	}

	public function email()
	{
		if(!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
			$this->errors[$this->name][] = $this->responses['email'];
		}
		return $this;
	}



	/**
	* Checks if array or other pattern
	* 
	* @param string 	$name
	* @return this
	*/
	private function pattern($name)
	{
		if($name == 'array') {
                
			if(!is_array($this->value)){
				$this->errors[$this->name][] = $this->responses['array'];
			}
            
		} else {
			$regex = '/^('.$this->patterns[$name].')$/u';
			if($this->value != '' && !preg_match($regex, $this->value)){
				$this->errors[$this->name][] = $this->responses['badFormat'];
			}
                
		}
		return $this;	
	}

	
	/** 
	 * Checks value for the specific pattern
	 *
	 * @param string 	$pattern
	 * @return this
	 */
	public function customPattern($pattern){

	$regex = '/^('.$pattern.')$/u';
	if($this->value != '' && !preg_match($regex, $this->value) && 
		ini_get("display_errors") != false || ini_get("display_errors") != 0 || ini_get("display_errors") != 'off'
	){
		throw new \Exception("Custom pattern \"{$pattern}\" is invalid.");
		
	}
	
	return $this;
	}


	public function validate()
	{
		$this->parseErrors();
	}

	private function parseErrors()
	{
		$array = array();

		if(count($this->errors)) {
			foreach ($this->errors as $key => $error) {
				$error = str_replace("{{field}}", ucfirst($this->name), $error);
				$error = str_replace("{{lenMin}}", $this->lengthMin, $error);
				$error = str_replace("{{lenMax}}", $this->lengthMax, $error);
				$error = str_replace("{{numMin}}", $this->numMin, $error);
				$error = str_replace("{{numMax}}", $this->numMax, $error);

				$array[$key] = $error;
			}
		}
		$this->errors = [];
		$this->errors = $array;
		$this->parsed = true;
	}

	public function getErrors($callback = null)
	{
		if(!$this->parsed) {
			$this->parseErrors();
		}

		if(!$callback) {
			return $this->errors;
		}

		return call_user_func($callback, $this->errors);
	}


	public function failed()
	{
		return count($this->errors) != 0 ? true : false;
	}

	private function setNewRuleSet($ruleSet = null)
	{
		if($ruleSet) {
			if(is_array($ruleSet)) {
				$missing_keys = [];
				foreach ($this->responses as $key => $value) {
					if(!array_key_exists($key, $ruleSet)) {
						array_push($missing_keys, $key);
					}
				}

				if(count($missing_keys)) {
					throw new \Exception("New rule has missing keys: '".implode("', '", $missing_keys)."'");
				}

				$this->responses = $ruleSet;

			} else {
				throw new \Exception("New rule set must be type of array");
			}

			$this->responses = $ruleSet;
		}
	}

	public function getFirstError()
	{
		if(count($this->errors)) {
			return $this->errors[key($this->errors)][0];
		}
		return '';
	}


}
