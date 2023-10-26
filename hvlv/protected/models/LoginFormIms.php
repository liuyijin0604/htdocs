<?php
/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginFormIms extends CFormModel
{
	public $user;
	public $pwd;
	public $vvc;
	private $ina;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules(){
		return array(
			array('user, pwd, vvc', 'required'),
			array('vvc', 'captcha', 'allowEmpty'=>false),
			array('pwd', 'authenticate'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params){
		if(!$this->hasErrors()){
			$this->_identity=new UserIdentity($this->user,$this->pwd);
			$this->_identity->authenticateIms();
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login(){
		$this->ina = $this->_identity->getIna();
		if($this->_identity===null){
			$this->_identity=new UserIdentity($this->user,$this->pwd);
			$this->_identity->authenticateIms();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE){
			Yii::app()->user->login($this->_identity);
			return true;
		}
		else
		{
			return false;
		}	
	}

	public function getIna()
	{
		return $this->ina;
	}

}
