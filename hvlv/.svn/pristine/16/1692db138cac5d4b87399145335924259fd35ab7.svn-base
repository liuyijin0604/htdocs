<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	private $ina;
	
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		
		$record = User::model()->find('(email = :u OR user = :u) AND active = 1', array(':u' => $this->username));

		if ($record === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if ($record->password !== md5($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}else if (Yii::app()->name != 'PEP' && $record->type == 35){
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else {
			$this->_id = $record->id;
			$this->setState('email', $record->email);
			$this->setState('name', $record->fname);
			$this->setState('grp', $record->type);
			$this->setState('org', $record->org_id);
			$this->setState('rpc', empty($record->extra['rpc'])? 0 : 1);
			//@Author:Nero @Date:2021/6/2 @Description:Set user.dpt_id into Yii:app()->user;
			$this->setState('dpt_id',$record->dpt_id);
			$this->errorCode = self::ERROR_NONE;
			$record->logLogin();
		}
		return !$this->errorCode;
	}
	
	public function authenticateIms() {
		
		$record = User::model()->find('(email = :u OR user = :u) AND active = 1', array(':u' => $this->username));
		if ($record === null) {
			$record = User::model()->find(['condition' => 'email = :u OR user = :u','params' => array(':u' => $this->username),'order' => 'id DESC']);
		}

		if ($record === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if ($record->password !== md5($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}else if (Yii::app()->name != 'PEP' && $record->type == 35){
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else {
			$this->_id = $record->id;
			$this->setState('email', $record->email);
			$this->setState('name', $record->fname);
			$this->setState('grp', $record->type);
			$this->setState('org', $record->org_id);
			$this->setState('rpc', empty($record->extra['rpc'])? 0 : 1);
			//@Author:Nero @Date:2021/6/2 @Description:Set user.dpt_id into Yii:app()->user;
			$this->setState('dpt_id',$record->dpt_id);
			if ($record->active != 1) {
				$this->errorCode = self::ERROR_USERNAME_INVALID;
				$this->ina=1;
			}
			else{
				$this->errorCode = self::ERROR_NONE;
				$record->logLogin();
			}			
		}
		return !$this->errorCode;
	}

	public function getOrg(){
		$r = User::model()->findByPk($this->_id);
		return $r->org;
	}

	public function getId(){
		return $this->_id;
	}

	public function getIna()
	{
		return $this->ina;
	}
}