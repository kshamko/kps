<?php

class Kps_Validate_UserEmail extends Zend_Validate_Abstract{

    const INVALID = 'Email exists';

    private $_exclude = array();

    protected $_messageTemplates = array(
        self::INVALID      => "%value% is already registered",
    );

    public function excludeEmail($email){
        $this->_exclude[] = $email;
    }

    public function isValid($value){

        if(in_array($value, $this->_exclude)){
            return true;
        }

        $oUser = new Model_Users();
        $user = $oUser->getUserByEmail($value);//->getData();

        if(!$user){
            return true;
        }else{            
            if(count($this->_exclude) && in_array($user['user_email'], $this->_exclude)){
                return true;
            }            
            $this->_error(self::INVALID, $value);
            return false;
        }
    }

}