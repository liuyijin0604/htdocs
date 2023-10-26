<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
    private $_isDriver;
	
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
        $this->errorCode = self::ERROR_NONE;

        $this->_isDriver = false;
        // only driver can login by user identity
		$record = User::model()->find('(email = :u OR user = :u) AND active = 1 AND type = 100', array(':u' => $this->username));

        $this->setState('driver', false);
        if ( $record === null ) {
            // we support customer login with custormer id and contact number
            // ...
            $org = Org::model()->findByPk(trim($this->username));
            if ( !empty($org) ) {
                if (!empty($org->extra['password'])) {
                    $password = $org->extra['password'];
                    if ($password == md5($this->password)) {
                        // we still get a real user id , if not existing in User table
                        // how to do this ???
                        $this->_id = $org->id;
                        $this->setState('email', $org->email);
                        $this->setState('name', $org->name);
                        $this->setState('grp', 80);
                        $this->setState('org', $org->id);
                        $this->setState('rpc', 0);
                        if (empty($org->address) || empty($org->suburb) || empty($org->state) || empty($org->postcode) || empty($org->phone) || empty($org->email)) {
                            $this->setState('incomplete', true);
                        } else {
                            $this->setState('incomplete', false);
                        }
                        $this->errorCode = self::ERROR_NONE;
                    } else {
                        $this->errorCode = self::ERROR_PASSWORD_INVALID;
                    }
                } else {
                    $password = trim($org->id);
                    if ( !empty($password) && $password == $this->password ) {
                        // we still get a real user id , if not existing in User table
                        // how to do this ???
                        $this->_id = $org->id;
                        $this->setState('email', $org->email);
                        $this->setState('name', $org->name);
                        $this->setState('grp', 80);
                        $this->setState('org', $org->id);
                        $this->setState('rpc', 0);
                        $this->setState('incomplete', true);
                        $this->errorCode = self::ERROR_NONE;
                    } else {
                        $this->errorCode = self::ERROR_PASSWORD_INVALID;
                    }
                }
            } else {
                $this->errorCode = self::ERROR_USERNAME_INVALID;
            }
            return !$this->errorCode;
        } else {
            if ( $record->type == 100 ) {
                $this->_isDriver = true;
                $this->setState('driver', true);
            }
        }

		if ($record === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		} else if ($record->password !== md5($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			$this->_id = $record->id;
			$this->setState('email', $record->email);
			$this->setState('name', $record->fname);
			$this->setState('grp', $record->type);
			$this->setState('org', $record->org_id);
			$this->setState('rpc', empty($record->extra['rpc'])? 0 : 1);
			$this->errorCode = self::ERROR_NONE;
			$record->logLogin();
		}
		return !$this->errorCode;
	}
	
	public function getOrg(){
        if ( $this->_isDriver ) {
            $r = User::model()->findByPk($this->_id);
            return $r->org;
        } else {
            $org = Org::model()->findByPk($this->_id);
            return $org;
        }
	}

    public function isDriver(){
        return $this->_isDriver;
    }

	public function getId(){
		return $this->_id;
	}
}