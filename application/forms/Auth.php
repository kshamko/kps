<?php

class forms_Auth extends Zend_Form {

    public function init() {
        parent::init();
        $this->setName('auth');
        $this->setAction('/user/auth/login');
        
        $em = Zend_Registry::get('doctrine_em');
        
        $email = new Zend_Form_Element_Text('user_email');
        $email->setDecorators(array(new Zend_Form_Decorator_ViewHelper()))
                ->addValidator(new Zend_Validate_EmailAddress())
                ->addValidator(new Kps_Validate_UserEmail(array('userModel'=>$em->getRepository('Model\Entities\User'))))                
                ->setLabel('Email');
        $this->addElement($email);
        
        $password = new Zend_Form_Element_Password('user_password');
        $password->setDecorators(array(new Zend_Form_Decorator_ViewHelper()))
                ->setLabel('Password');        
        $this->addElement($password);
        
        $submit = new Zend_Form_Element_Submit('Log In');
        $submit->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($submit);         
        
    }
}


