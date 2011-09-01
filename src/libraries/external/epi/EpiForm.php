<?php
/**
  * EpiForm for server-side form validation
  *
  * @author Kevin Hornschemeier <khornschemeier@gmail.com>
  */
class EpiForm
{
	private static $instance;

	public static function getInstance()
  {
    if(self::$instance)
      return self::$instance;

    self::$instance = new EpiForm;
    return self::$instance;
  }

	/************************************************************************************************************
		Form::hasErrors

		Parameters
			$input: assoc array			array( array($displayName, $value, $validationType[, $arg1, $arg2, ...]), array($displayName, $value, $validationType[, $arg1, $arg2, ...]), ... )

		Types + additional arguments
			email					no additional arguments
			match					matchDisplayName, matchValue
			required			no additional arguments

		Examples
			$input = array(
				array('Company', $company, 'required'),
				array('Email', $email, 'required email'),
				array('Password', $password, 'required match', 'Confirm Password', $confirmPassword),
				array('Confirm Password', $confirmPassword, 'required'),
			);
			$errors = Form::hasErrors($input);

		Returns
			false or assoc array of messages
	************************************************************************************************************/
	public static function hasErrors($input)
	{
		$errors = array();

		foreach($input as $k => $v)
		{
			$displayName = array_shift($v);
			$value = array_shift($v);
			$validationType = explode(' ', array_shift($v));
			$arguments = $v;

			foreach($validationType as $type)
			{
				switch($type)
				{
					case 'date':
						if(!self::passesDate($value))
							$errors[] = "{$displayName} is not a valid date";
						break;

					case 'email':
						if(!self::passesEmail($value))
							$errors[] = "{$displayName} is not a valid email address";
						break;

					case 'ifexists':
						if(!empty($value))
							$errors = array_merge($errors, self::hasErrors($arguments));
						break;

					case 'integer':
						if(!self::passesInteger($value))
							$errors[] = "{$displayName} is not a number";
						break;

					case 'match':
						$matchDisplayName = array_shift($arguments);
						$matchValue = array_shift($arguments);

						if(!self::passesMatch($value, $matchValue))
							$errors[] = "{$displayName} does not match {$matchDisplayName}";
						break;

					case 'required':
						if(!self::passesRequired($value))
							$errors[] = "{$displayName} is required";
						break;
				}
			}
		}

		return (empty($errors) ? false : $errors);
	}

	/************************************************************************************************************
		Form::passesDate

		Parameters
			$value		value of the form field

		Returns
			true or false
	************************************************************************************************************/
	private static function passesDate($value)
	{
		return preg_match('#^\d{1,2}/\d{1,2}/\d{4}$#', $value);
	}

	/************************************************************************************************************
		Form::passesEmail

		Parameters
			$value		value of the form field

		Returns
			true or false
	************************************************************************************************************/
	private static function passesEmail($value)
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	/************************************************************************************************************
		Form::passesInteger

		Parameters
			$value		value of the form field

		Returns
			true or false
	************************************************************************************************************/
	private static function passesInteger($value)
	{
		return filter_var($value, FILTER_VALIDATE_INT);
	}

	/************************************************************************************************************
		Form::passesMatch

		Parameters
			$value				value of the form field
			$matchValue		value of the field to match

		Returns
			true or false
	************************************************************************************************************/
	private static function passesMatch($value, $matchValue)
	{
		return $value == $matchValue;
	}

	/************************************************************************************************************
		Form::passesRequired

		Parameters
			$value		value of the form field

		Returns
			true or false
	************************************************************************************************************/
	private static function passesRequired($value)
	{
		return !empty($value);
	}
}


function getForm()
{
  return EpiForm::getInstance();
}