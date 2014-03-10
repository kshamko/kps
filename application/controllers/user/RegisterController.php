<?php

/**
 * Controller to handle authentification if users 
 * @author Konstantin Shamko
 */
class user_RegisterController extends Kps_Controller {

    /**
     * @acl_groups guest
     */
    public function indexAction() {

        $form = new forms_Register();

        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();

            if ($form->isValid($data)) {

                $user = new Model\Entities\User;
                $user->setUserEmail($data['user_email'])
                     ->setUserPassword(md5($data['user_password']))
                     ->setUserStatus(0)
                     ->setUserRegistrationDate(time())   
                     ->setUserGroup('member');
                
                $this->_em->persist($user);
                $this->_em->flush();
                
                //notify via email
                
            }
        }

        $this->view->form = $form;
    }

}
