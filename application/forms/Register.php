<?php

class forms_Register extends Zend_Form {

    public function init() {
        parent::init();
       
        $this->setName('Register');
        $this->setAction('/user/register');
        $this->setMethod('POST');

        
        $em = Zend_Registry::get('doctrine_em');
        
        $email = new Zend_Form_Element_Text('user_email');
        $email->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $email->setRequired(true);
        $email->addValidator(new Zend_Validate_EmailAddress())
                ->addValidator(new Kps_Validate_UserEmail(array('userModel'=>$em->getRepository('Model\Entities\User'))))
                ->setLabel('Email');
        $this->addElement($email);
        
        $password = new Zend_Form_Element_Password('user_password');
        $password->setRequired(true)
                ->setDecorators(array(new Zend_Form_Decorator_ViewHelper()))
                ->setLabel('Password');
        
        $this->addElement($password);
        
        $passConfirm = new Zend_Form_Element_Password('user_password_confirm');
        $passConfirm->setRequired(true)
                    ->setDecorators(array(new Zend_Form_Decorator_ViewHelper()))
                    ->addValidator(new Zend_Validate_Identical('user_password'))
                    ->setLabel('Confirm Password');
        
        $this->addElement($passConfirm);        

        $submit = new Zend_Form_Element_Submit('Register');
        $submit->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($submit);
        
    }

}
