<?php
/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ClientLoginForm extends CFormModel
{
	public $user;
	public $pwd;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules(){
		return array(
			array('user, pwd', 'required'),
			array('pwd', 'authenticate'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params){
		if(!$this->hasErrors()){
			$this->_identity=new ClientIdentity($this->user,$this->pwd);
			$this->_identity->authenticate();
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login(){
		if($this->_identity===null){
			$this->_identity=new ClientIdentity($this->user,$this->pwd);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===ClientIdentity::ERROR_NONE){
			Yii::app()->user->login($this->_identity);
			return true;
		}
		else
			return false;
	}
}
