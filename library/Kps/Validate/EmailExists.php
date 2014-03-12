<?php

class Kps_Validate_EmailExists extends Zend_Validate_Abstract{

    const INVALID = 'Email not exists';

    private $_exclude = array();
    private $_userModel = null;
    
    protected $_messageTemplates = array(
        self::INVALID      => "%value% not found",
    );
    
    public function __construct($options = null) {
        if(is_array($options) && isset($options['userModel']) && is_object($options['userModel'])){
            $this->_userModel = $options['userModel'];
        }else{
            throw new \Exception('Please set proper userModel option for '.__CLASS__);
        }
    }

    public function isValid($value){

        if(in_array($value, $this->_exclude)){
            return true;
        }

        $user = $this->_userModel->getUserByEmail($value);
        
        if(!$user){
            $this->_error(self::INVALID, $value);
            return false;
        }
        
        return true;
    }

}