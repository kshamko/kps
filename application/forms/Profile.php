<?php

class forms_Profile extends Kps_Form_DoctrineEntity {

    /**
     * Email address to ignore exist validation
     * 
     * @var string
     */
    private $_ignoreEmail = null;

    public function __construct($options = null) {

        if (isset($options['ignore_email'])) {
            $this->_ignoreEmail = $options['ignore_email'];
            unset($options['ignore_email']);
        }
        parent::__construct($options);
    }

    public function init() {
        parent::init();

        $em = Zend_Registry::get('doctrine_em');

        $this->setName('Profile');
        $this->setAction('/user/profile');
        $this->setMethod('POST');
        $this->setEntityManager($em);


        $emailExistsValidator = new Kps_Validate_UserEmail(array('userModel' => $em->getRepository('Model\Entities\User')));
        if ($this->_ignoreEmail) {
            $emailExistsValidator->excludeEmail($this->_ignoreEmail);
        }

        $email = new Zend_Form_Element_Text('user_email');
        $email->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $email->setRequired(true);
        $email->addValidator(new Zend_Validate_EmailAddress())
                ->addValidator($emailExistsValidator)
                ->setLabel('Email');
        $this->addElement($email);
        $this->mapElementToEntity($email, 'userEmail');


        $firstName = new Zend_Form_Element_Text('user_first_name');
        $firstName->setLabel('First Name')
                ->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($firstName);
        $this->mapElementToEntity($firstName, 'userFirstName');

        $lastName = new Zend_Form_Element_Text('user_last_name');
        $lastName->setLabel('Last Name')
                ->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($lastName);
        $this->mapElementToEntity($lastName, 'userLastName');

        $submit = new Zend_Form_Element_Submit('Update');
        $submit->setDecorators(array(new Zend_Form_Decorator_ViewHelper()));
        $this->addElement($submit);
        
        $this->addElement(new Zend_Form_Element_Hidden('form_action', array('value'=>'profile')));
    }

}
