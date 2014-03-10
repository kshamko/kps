<?php

class forms_Password extends Zend_Form {

    public function init() {
        parent::init();

        $this->setName('Password');
        $this->setAction('/user/profile');
        $this->setMethod('POST');

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

        $submit = new Zend_Form_Element_Submit('Change Password');
        $submit->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($submit);
    }

}
