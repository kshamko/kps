<?php

class Kps_Validate_UserName extends Zend_Validate_Abstract{

    const INVALID = 'Name_exists';
    const SPACES = 'Spaces_surrounding';

    private $_exclude = array();

    protected $_messageTemplates = array(
        self::INVALID      => "Username %value% is already registered",
        self::SPACES      => "Username cannot contain spaces in the beginning or in the end",
    );

    public function excludeUsername($username){
        $this->_exclude[] = $username;
    }

    public function isValid($value){

        if(in_array($value, $this->_exclude)){
            return true;
        }
        
        if(trim($value)!=$value) {
            $this->_error(self::SPACES, $value);
            return false;
        }

        $oUser = new Model_Users();
        $user = $oUser->getUserByLogin($value);

        if(!$user){
            return true;
        }else{            
            if(count($this->_exclude) && in_array($user['user_login'], $this->_exclude)){
                return true;
            }            
            $this->_error(self::INVALID, $value);
            return false;
        }
    }

}