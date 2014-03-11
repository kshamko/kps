<?php

class Kps_Validate_UserEmail extends Zend_Validate_Abstract{

    const INVALID = 'Email exists';

    private $_exclude = array();
    private $_userModel = null;
    
    protected $_messageTemplates = array(
        self::INVALID      => "%value% is already registered",
    );
    
    public function __construct($options = null) {
        if(is_array($options) && isset($options['userModel']) && is_object($options['userModel'])){
            $this->_userModel = $options['userModel'];
        }else{
            throw new \Exception('Please set proper userModel option for Kps_Validate_UserEmail');
        }
    }
    
    public function excludeEmail($email){
        $this->_exclude[] = $email;
    }

    public function isValid($value){

        if(in_array($value, $this->_exclude)){
            return true;
        }

        $user = $this->_userModel->getUserByEmail($value);
        
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