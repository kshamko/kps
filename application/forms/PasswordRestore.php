<?php

class forms_PasswordRestore extends Zend_Form {

    public function init() {
        parent::init();
        $this->setName('auth');
        $this->setAction('/user/password');
        
        $email = new Zend_Form_Element_Text('user_email');
        $email->setDecorators(array(new Zend_Form_Decorator_ViewHelper()))
                ->addValidator(new Zend_Validate_EmailAddress())
                ->setLabel('Email');
        $this->addElement($email);
        
        $submit = new Zend_Form_Element_Submit('Restore');
        $submit->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($submit);         
        
    }
}


