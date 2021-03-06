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

                $code = md5(microtime() . rand(1000, 9999));

                $user = new Model\Entities\User;
                $user->setUserEmail($data['user_email'])
                        ->setUserPassword(md5($data['user_password']))
                        ->setUserStatus(0)
                        ->setUserRegistrationDate(new DateTime('now'))
                        ->setUserGroup('member')
                        ->setUserActivationCode($code);

                $this->_em->persist($user);
                $this->_em->flush();

                //notify via email
                $mailer = new Helper_Mail();
                $mailer->setBody('activate_account', array('code' => $code))
                        ->setSubject('Please activate your account')
                        ->setRecipient($data['user_email'])
                        ->send();

                $this->_messages->setMessage('Your account has been created! Please activate it!');
                $this->_redirect('/user/auth');

                //login
                //$this->_auth->getStorage()->write($user);
                //$this->_redirect('/user/profile');
            }
        }

        $this->view->form = $form;
    }

    /**
     * @acl_groups guest, member, root
     */
    public function activateAction() {
        $message = 'Activation code was not found.';
        $messageType = 'error';

        $code = $this->_request->getParam('code');

        if ($code) {

            $this->_em->getRepository('Model\Entities\User');
            $user = $this->_em
                    ->getRepository('Model\Entities\User')
                    ->getUserByActivationCode($code);

            if ($user) {

                $message = 'Your account has been activated.';
                $messageType = '';

                $user->setUserStatus(1)
                     ->setUserActivationCode(null);

                $this->_em->flush($user);
            }else{
                $message = 'User was not found.';
            }


        } 

        $this->_messages->setMessage($message, $messageType);
        $this->_redirect('/');
    }

}
